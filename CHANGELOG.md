# Release Notes for Delete Unused Assets

## 2.0.1 – 2023.12.14
### Fixed
* Fixed a bug where size values could be out of range for the database in case of volumes with many assets ([#2](https://github.com/orbital-flight/craft-delete-assets/issues/2)).

## 2.0.0 – 2023.11.22
### Added
* **AUTOSCAN**: The plugin automatically starts a scan if changes were previously made to a volume. *(You can disable this feature in the plugin settings. The scan is only triggered when loading the plugin page).*
* Added a button to scan all availables volumes at once! 
* You can now manage access and usage of the plugin in the user permissions management.
* Added the time since last scan for each volume
* Added a badge featuring the number of freeable assets

### Changed
* Lifted the admin requirement to delete assets in favor of permissions.
* Improved the detection of changes in volumes to mark them as outdated.
* The page now scrolls itself to the scanned volume after its analyze or its cleanup.
* Improved messages and translations.

## 1.1.2 - 2023.10.02
### Added
* Added French translation. Bonjour 🔥

## 1.1.1 - 2023.10.02
### Fixed
* Fixed a bug where the scan crashed to a `TypeError` when an `Asset` was related to an inexistent `Element`.

### Changed
* Made the default green less dull


## 1.1.0 - 2023.09.23
### Added
* You can now change the default colors for the assets usage display.

## 1.0.0 - 2023.09.09
* Initial release
