# Snipcart Changelog

## 1.0.4 - 2019-03-09
### Added
- Added Twig template methods: `craft.snipcart.getCustomer()`, `craft.snipcart.getOrder()` and `craft.snipcart.getSubscription()`.
- Added ability to override Twig `getBuyNowButton()` price with support for multiple currencies.
- `getBuyNowButton()` can now include custom options with negative price adjustments.
### Fixed
- Currency setting is now saved properly.
- Improved consistency of currency values displayed in the control panel and email templates.
- Fixed UnknownPropertyException when viewing Discounts because of new `normalizedRate` property.

## 1.0.3 - 2019-03-05
### Fixed
- Fixed a bug where passing a `null` value for Product Details `customOptions` would throw a warning in PHP 7.2. 

## 1.0.2 - 2019-03-04
### Fixed
- Fixed bug populating existing Element's product detail.

## 1.0.1 - 2019-03-03
### Added
- Added support for pre-3.1 versions of Craft CMS.
### Fixed
- Fixed an issue that would cause a Section re-save task to fail after a Product Details field is added to an existing Section.
- Product defaults are honored correctly per field setting on new and existing entries.
- Fixed a JS error in Product Details field settings.

## 1.0.0 - 2019-02-27
### Changed
- Removed beta tag! 🎉

## 1.0.0-beta.27 - 2019-02-27
### Fixed
- Improved safety of CraftQL check.

## 1.0.0-beta.26 - 2019-02-27
### Fixed
- Fixed Install migration to prevent blocking re-install.
### Changed
- Added a nicer configured+empty landing state graphic.

## 1.0.0-beta.25 - 2019-02-26
### Fixed
- Cleaned up docblocks and variable names for consistency.
### Changed
- Required endpoint parameter for `get()` , `post()`, `put()`, and `delete()` API service methods.

## 1.0.0-beta.24 - 2019-02-24
### Fixed
- Fixed a bug that kept static config settings from counting toward a configured state.

## 1.0.0-beta.23 - 2019-02-24
### Added
- Added pagination support to Abandoned Carts.
- Added a friendlier CP section empty state before plugin is configured.
### Changed
- Ajaxified CP landing stat panels to speed up page load.
- Spiffed up the Craft Commerce comparison table in the readme.
### Fixed
- Fixed template error when `shipFrom` settings are empty.
- Fixed incorrect reference that interfered with subscription invoice creation webhook.
- Invalid/unparsed environment variables won't count as a "configured" state for the plugin.

## 1.0.0-beta.22 - 2019-02-18
### Added
- Added Notifications service.
### Fixed
- Fixed bug in console Snipcart → ShipStation verification utility.

## 1.0.0-beta.21 - 2019-02-17
### Added
- Added CraftQL support for field data.
- Improved support for [Webhooks plugin](https://github.com/craftcms/webhooks).
### Changed
- Isolated API caches with TagDependency.
- Refactored webhooks into component.
### Fixed
- Stopped inventory event from firing for products that don't store inventory.

## 1.0.0-beta.20 - 2019-02-17
### Added
- Added support for additional AbandonedCart attributes.

## 1.0.0-beta.19 - 2019-02-16
### Added
- Added Fields service.
### Fixed
- Improved webhook resilience in some cases when incoming payloads contain unexpected attributes.

## 1.0.0-beta.18 - 2019-02-15
### Added
- Added new Order and Item attributes.

## 1.0.0-beta.17 - 2019-02-14
### Added
- Added inventory to the Product Details field type.
- Added the ability to designate a custom admin order notification email template.
- Webhook log now stores `mode`.
- Added support for displaying Subscriptions and cancelling them from the control panel.
- Added Overview tab for control panel section.
- Added Event hook for registering shipping providers.
- Some settings now offer env and template suggestions.
- Added environment variable support for Snipcart + ShipStation API keys.
### Changed
- Flattened migrations into single Install.
- Improved several control panel views.
- Optimized control panel assets.
- Vastly improved speed and flexibility of Dashboard widget.
- Refactored control panel views.
### Removed
- Deleted many SnipcartVariable methods after refactor.
- Cleaned up Orders interface, removing several methods.
- Removed ability to define custom product fields.
- Removed unused Packaging Types.

## 1.0.0-beta.16 - 2018-12-27
### Added
- Added Product Details field type for quick setup.
- Added control panel views for Discounts, Abandoned Carts, and Subscriptions.
- Added ability to create a Discount via control panel.
### Changed
- Improved plugin settings control panel layout.
- Refactored and expanded Events.
- References to `WebhookEvent` should now be `ShippingRateEvent`.

## 1.0.0-beta.15 - 2018-12-24
### Changed
- Refactored ShippingProvider to expose REST API methods.
- Various code quality improvements.
### Fixed
- Fixed incorrect item weights when converting a Snipcart order into a ShipStation order.

## 1.0.0-beta.14 - 2018-12-20
### Changed
- ShipStation's `_getOrderNotes()` and `_getGiftNote()` will no longer return empty values.

## 1.0.0-beta.13 - 2018-12-20
### Changed
- **Breaking changes for everyone!**
- Massively refactored services and models, which will definitely break any services or models you're using directly.
- Renamed all ShipStation models and moved them to their own namespace.
- Renamed all Snipcart models.
- `WebhookEvent`'s `packaging` property is now `package`.
- Abstracted ShipStation service into Shipments, meaning it's now accessed via `Snipcart::$plugin->shipments->shipStation`. Note that the intent is for other services to interact directly with Shipments, to which `EVENT_BEFORE_RETURN_SHIPPING_RATES` has moved. Listeners should subscribe to `Shipments::EVENT_BEFORE_RETURN_SHIPPING_RATES`.
- Listeners should now subscribe to `Orders::EVENT_BEFORE_REQUEST_SHIPPING_RATES` instead of `SnipcartService::EVENT_BEFORE_REQUEST_SHIPPING_RATES`.
- Listeners should now subscribe to `Products::EVENT_PRODUCT_INVENTORY_CHANGE` instead of `SnipcartService::EVENT_PRODUCT_INVENTORY_CHANGE`.

## 1.0.0-beta.12 - 2018-12-18
### Fixed
- Fixed missing ShipStation fields, prevent wrapping additional email prices.

## 1.0.0-beta.11 - 2018-12-17
### Fixed
- Fixed token verification.

## 1.0.0-beta.10 - 2018-12-17
### Changed
- VerifyController requests un-cached Snipcart orders.

## 1.0.0-beta.9 - 2018-12-17
### Added
- Spiffed up and added re-feed attempt status to order failure notifications.
### Changed
- Added explicit type coercion in a few places.
### Fixed
- Fixed type error that impacted VerifyController.
- Reverted ShipStationOrderItem `fields()`.

## 1.0.0-beta.8 - 2018-12-17
### Added
- Separated parts of SnipcartService into new ApiService.
### Changed
- Changed the way API exceptions are handled to reduce disruption and log failures.
- Refactored SnipcartService to be cleaner.
- Renamed SnipcartService's `processShippingRates()` to `getShippingRatesForOrder()`.
- Models now use proper DateTime values.
- Improved ShipStation order verifier accuracy.
- Updated webhook controller's `handleOrderCompletedEvent()` to continue through problems and report errors by model. Any errors at all will result in `success: false`.
- Renamed `listAbandoned` template variable to `listAbandonedCarts`.
### Fixed
- Minor fix for mobile order email price wrapping.

## 1.0.0-beta.7 - 2018-12-15
### Added
- Added package detail to Snipcart rate response.
### Changed
- Use billingAddressName instead of cardholderName in order notification emails.
- Return JSON for all webhook requests.
- Keep ShipStation service from failing if Snipcart order has `null` value for custom fields.
- Rename webhook controller's `badResponse()` to `badRequestResponse()`.
### Fixed
- Fix type issues with SnipcartOrder model.
- Return magic variables when SnipcartOrder is treated as an array.
- Respond calmly to missing webhook event names or content and don't allow logging.

## 1.0.0-beta.6 - 2018-12-14
### Added
- Added weight property to SnipcartItem model.
- Added hasPhysicalDimensions() for both Snipcart and ShipStation item models.
### Changed
- Changed ShipStation service method names to better reflect what they do.
### Fixed
- Fixed incorrect docblock details.

## 1.0.0-beta.5 - 2018-12-13
### Changed
- ShipStationService's getWeightFromSnipcartData() is now getWeightFromSnipcartOrder().

## 1.0.0-beta.4 - 2018-12-13
### Changed
- Improved code quality throughout models.
### Fixed
- Fixed SnipStation gift setting detection.

## 1.0.0-beta.3 - 2018-12-13
### Changed
- Made cosmetic fixes to console order verification tool.
- ShipStation order model no longer limits string length; longer customer and gift messages won't cause webhook failure.
### Fixed
- Fixed webhook and service bugs.

## 1.0.0-beta.2 - 2018-12-13
### Changed
- Improved class documentation.
- Improved console verification tool.
- Improved code quality with optimizations and type hints.
### Fixed
- Fixed several incorrect references.

## 1.0.0-beta.1 - 2018-12-10
### Added
- Initial GitHub release.
