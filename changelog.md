# Snipcart Changelog

## 1.0.0-beta.6 - 2018-12-14
### Changed
- Changed ShipStation service method names to better reflect what they do.
- Added weight property to SnipcartItem model.
- Added hasPhysicalDimensions() for both Snipcart and ShipStation item models.
- Fixed incorrect docblock details.

## 1.0.0-beta.5 - 2018-12-13
### Changed
- ShipStationService's getWeightFromSnipcartData() is now getWeightFromSnipcartOrder().

## 1.0.0-beta.4 - 2018-12-13
### Changed
- Improved code quality throughout models.
- Fixed SnipStation gift setting detection.

## 1.0.0-beta.3 - 2018-12-13
### Changed
- Made cosmetic fixes to console order verification tool.
- ShipStation order model no longer limits string length; longer customer and gift messages won't cause webhook failure.
- Fixed webhook and service bugs.

## 1.0.0-beta.2 - 2018-12-13
### Changed
- Improved class documentation.
- Improved console verification tool.
- Improved code quality with optimizations and type hints.
- Fixed several incorrect references.

## 1.0.0-beta.1 - 2018-12-10
### Added
- Initial GitHub release.
