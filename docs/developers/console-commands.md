# Console Commands

Snipcart includes console commands you can run from your Craft project’s directory.

## Orders

::: reference
### `snipcart/verify/check-orders`

Checks that your most recent Snipcart orders with shippable items made it to ShipStation. Any order missing from ShipStation is sent again if it was created within the [`reFeedAttemptWindow`](docs:get-started/configuration#refeedattemptwindow) setting (15 minutes by default), and an email listing the missing orders is sent to your [`notificationEmails`](docs:get-started/configuration#notificationemails) addresses. Run this on a schedule, such as a cron job, to recover orders that failed to reach ShipStation.

The command takes two positional arguments, in this order.

| Argument | Description | Default |
| --- | --- | --- |
| `forceFeed` | Send every missing order to ShipStation again, regardless of when it was created. Use `1` or `0`. | `0` |
| `limit` | The number of recent orders to check. | `3` |

```shell
php craft snipcart/verify/check-orders 0 10
```
:::

## Maintenance

::: reference
### `snipcart/cleanup`

Drops the `snipcart_product_details` database table, deleting all stored Product Details field values. This can’t be undone, so only run it when you’re removing Snipcart’s data, and back up your database first.

```shell
php craft snipcart/cleanup
```
:::
