# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [unreleased]

## [v1.1.0] - 2016-10-10

### Added
- Guest/customer mail review and resending grid

### Fixed
- Fix resending: In case only one recipient is available, no implode
  several email addresses must take place.

## [v1.0.6] - 2016-09-05
### Fixed
- On loop mail model is now reset on transport creation

## [v1.0.5] - 2016-09-05
### Fixed
- SMTP attachment encoding default is now set to base64 if not given

## [v1.0.3] - 2016-08-30
### Fixed
- Handover stroreid to get from name and mail for specific store

## [v1.0.2] - 2016-08-30
### Fixed
- Bugfix for RMA resending

## [v1.0.0] - 2016-08-25

### Added
- Apache2 license
- First working adminhtml customer mail tracking

## Fixed
- Multi-language cron scheduled mails

### Changed
- Rename all "storeage" to "storage"
