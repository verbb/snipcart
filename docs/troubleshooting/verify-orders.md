---
meta:
  - name: description
    content: Using the console order verifier to confirm Snipcart orders made it to ShipStation.
---

# Verifying ShipStation Orders

There's a console utility you can use to make sure orders are fed to ShipStation properly.

```shell
./craft snipcart/verify/check-orders
```

Running it will fetch the most recent three Snipcart orders, then query ShipStation for each one to confirm that an order with a matching invoice exists.

```shell
-------------------------------------
Checking last 3 orders...
-------------------------------------
Snipcart SNIP-5556 … ShipStation [✓]
Snipcart SNIP-5557 … ShipStation [✓]
Snipcart SNIP-5558 … ShipStation [✓]
-------------------------------------
Finished in 3.0893619060516 seconds.
```

If the order does not exist in ShipStation, the plugin will attempt to re-feed it again. Any admin email addresses established in the plugin settings will receive an email summary of the missing ShipStation orders and results of the re-feed attempt.