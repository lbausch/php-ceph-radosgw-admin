# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [Unreleased]
### Added
### Changed
### Deprecated
### Removed
### Fixed
### Security


## [0.3.0] - 2024-12-09
### Changed
Breaking: Remove support for PHP 8.2


## [0.2.0] - 2021-09-09
### Added
- Allow to pass arbitrary options when creating S3 client
- Support removing objects from buckets
- Support creating, modifying and removing subusers
- Support adding and removing capabilities
- Support managing user quotas
- Support managing bucket quotas
- Support setting quota on individual bucket
- Added more tests
### Changed
- Breaking: Use `remove` instead of `delete` for some method names
- Improved README
### Fixed
- Use correct API endpoint when reading the policy of an object or bucket


## [0.1.0] - 2021-09-06
### Added
- Initial release


[Unreleased]: https://github.com/lbausch/php-ceph-radosgw-admin/compare/v0.3.0...HEAD
[0.3.0]: https://github.com/lbausch/php-ceph-radosgw-admin/releases/tag/v0.3.0
[0.2.0]: https://github.com/lbausch/php-ceph-radosgw-admin/releases/tag/v0.2.0
[0.1.0]: https://github.com/lbausch/php-ceph-radosgw-admin/releases/tag/v0.1.0
