# Change Log

## [1.1.2] - Septevmer 10, 2020
### Changed
- Removed jQuery dependency (now uses vanilla JS)

## [1.1.1] - September 4, 2020
### Changed
- Made JavaScript less greedy (also fixes some incompatibilities with other plugins)

## [1.1.0] - September 2, 2020
Tested in WP 5.5

### Added
- Support for freshbooks accounts with over 100 clients

### Changed
- Plugin no longer checks for freshbooks client ID upon user registration. This may require users to log out and back in again after registering to view their data. Only an issue where auto-login after registration is enabled.
- Changed jQuery variables to be standard JS variables.
- Updated frontend table design

### Removed
- Empty deactivation hook
- Broken WP Nonces; Will be re-adding after fixing

### Fixed
- Issue where users could not log in if a Freshbooks account was connected.

## [1.0.2] - June 17, 2020
### Added
- Internationalization of all text strings
- Escaped all outputted texted

### Changed
- Moved inline styles to external stylesheet
- Adjusted pagination to work on pages with existing query parameters

## [1.0.1] - June 13, 2020
### Added
- Sanitize custom DB settings
- Navigation tabs on settings page
- Option to choose if data is removed on plugin uninstall

### Changed
- Refactored cURL Functions to use WP_Http_Curl class

## [1.0.0] - June 4, 2020
Plugin Created