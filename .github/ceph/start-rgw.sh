#!/usr/bin/env bash
#
# Boot a throwaway single-node Ceph cluster (mon, mgr, in-memory OSD and RGW) inside an official Ceph container image.
# Used for integration tests only.
#
#   docker run --detach --name rgw --publish 8080:8080 \
#       --volume "$PWD/.github/ceph:/ci:ro" --entrypoint /ci/start-rgw.sh \
#       quay.io/ceph/ceph:v19
#
# The container logs "RGW ready" once the admin API accepts requests.
set -euo pipefail

RGW_PORT="${RGW_PORT:-8080}"
ACCESS_KEY="${ACCESS_KEY:-admin-access-key}"
SECRET_KEY="${SECRET_KEY:-admin-secret-key}"

FSID="$(uuidgen)"
MON_IP=127.0.0.1

wait_for() {
    local description="$1"
    shift

    for _ in $(seq 1 120); do
        if "$@" > /dev/null 2>&1; then
            return 0
        fi

        sleep 1
    done

    echo "Timed out waiting for ${description}" >&2
    tail -n 50 /var/log/ceph/*.log >&2 || true
    exit 1
}

mkdir -p /etc/ceph /var/lib/ceph/{mon/ceph-a,mgr/ceph-x,osd/ceph-0} /var/log/ceph /var/run/ceph

cat > /etc/ceph/ceph.conf <<EOF
[global]
fsid = ${FSID}
mon host = ${MON_IP}
auth cluster required = none
auth service required = none
auth client required = none
osd objectstore = memstore
memstore device bytes = 2147483648
osd pool default size = 1
osd pool default min size = 1
osd crush chooseleaf type = 0
mon allow pool size one = true
mon allow pool delete = true
mon warn on pool no redundancy = false
mon data avail warn = 1
mon data avail crit = 0
log file = /var/log/ceph/\$cluster-\$name.log

[client.rgw.x]
rgw frontends = beast port=${RGW_PORT}
rgw enable usage log = true
rgw usage log tick interval = 1
rgw usage log flush threshold = 1
EOF

# Monitor
monmaptool --create --add a "${MON_IP}" --fsid "${FSID}" /tmp/monmap
ceph-authtool --create-keyring /tmp/mon.keyring --gen-key -n mon. --cap mon 'allow *'
ceph-mon --mkfs -i a --monmap /tmp/monmap --keyring /tmp/mon.keyring
ceph-mon -i a

# Manager
ceph-mgr -i x

# OSD backed by memory
OSD_UUID="$(uuidgen)"
OSD_ID="$(ceph osd new "${OSD_UUID}")"
ceph-osd -i "${OSD_ID}" --mkfs --osd-uuid "${OSD_UUID}"

# ceph-osd occasionally aborts during start-up when it contacts the monitor before having received the monmap, so retry a few times
for attempt in 1 2 3; do
    if ceph-osd -i "${OSD_ID}"; then
        break
    fi

    if [ "${attempt}" -eq 3 ]; then
        echo "ceph-osd failed to start" >&2
        exit 1
    fi
done

wait_for "OSD to be up and in" sh -c "ceph osd stat | grep -q '1 up.*1 in'"

# RGW
radosgw -n client.rgw.x

wait_for "RGW to accept requests" curl --silent --output /dev/null "http://127.0.0.1:${RGW_PORT}"

radosgw-admin user create \
    --uid=admin \
    --display-name="Admin User" \
    --admin \
    --caps="users=*;usage=*;buckets=*;metadata=*;zone=*;info=*;ratelimit=*;accounts=*" \
    --access-key="${ACCESS_KEY}" \
    --secret-key="${SECRET_KEY}" > /dev/null

echo "RGW ready: $(ceph --version)"

# Keep the container running and surface RGW's log
exec tail -F /var/log/ceph/ceph-client.rgw.x.log
