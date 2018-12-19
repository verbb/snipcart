# Snipcart Changelog

## 1.0.0-beta.13 - 2018-12-18
### Changed
- Massively refactored services and models, which is a **hugely breaking change if you're working with any services or models in your own code**!
- Renamed all ShipStation models and moved them to their own namespace.
- Renamed all Snipcart models.
- `WebhookEvent`'s `packaging` property is now `package`.

## 1.0.0-beta.12 - 2018-12-18
### Changed
- Fixed missing ShipStation fields, prevent wrapping additional email prices.

## 1.0.0-beta.11 - 2018-12-17
### Changed
- Fixed token verification.

## 1.0.0-beta.10 - 2018-12-17
### Changed
- VerifyController requests un-cached Snipcart orders.

## 1.0.0-beta.9 - 2018-12-17
### Changed
- Fixed type error that impacted VerifyController.
- Reverted ShipStationOrderItem `fields()`.
- Added explicit type coercion in a few places.
- Spiffed up and added re-feed attempt status to order failure notifications.

## 1.0.0-beta.8 - 2018-12-17
### Added
- Separated parts of SnipcartService into ApiService.
### Changed
- Changed the way API exceptions are handled to reduce disruption and log failures.
- Refactored SnipcartService to be cleaner.
- Renamed SnipcartService's `processShippingRates()` to `getShippingRatesForOrder()`.
- Models now use proper DateTime values.
- Minor fix for mobile order email price wrapping.
- Improved ShipStation order verifier accuracy.
- Updated webhook controller's `handleOrderCompletedEvent()` to continue through problems and report errors by model. Any errors at all will result in `success: false`.
- Renamed `listAbandoned` template variable to `listAbandonedCarts`.

## 1.0.0-beta.7 - 2018-12-15
### Changed
- Use billingAddressName instead of cardholderName in order notification emails.
- Add package detail to Snipcart rate response.
- Return JSON for all webhook requests.
- Keep ShipStation service from failing if Snipcart order has `null` value for custom fields.
- Fix type issues with SnipcartOrder model.
- Return magic variables when SnipcartOrder is treated as an array.
- Respond calmly to missing webhook event names or content and don't allow logging.
- Rename webhook controller's `badResponse()` to `badRequestResponse()`.

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
