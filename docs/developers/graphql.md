# GraphQL
If you're using Craft Pro, the included [Product Details Field Type](docs:feature-tour/products) can be queried with Craft's native GraphQL. Use your Field Type handle and specify whichever product details you need:

```graphql
myFieldHandle {
    sku
    price
    shippable
    taxable
    weight
    weightUnit
    length
    width
    height
    inventory
    dimensionsUnit
}
```

Example JSON result:

```json
"myFieldHandle": {
    "sku": "infinity-gauntlet",
    "price": 499.98,
    "shippable": true,
    "taxable": false,
    "weight": 1360.776,
    "weightUnit": "grams",
    "length": 36,
    "width": 21,
    "height": 21,
    "inventory": null,
    "dimensionsUnit": "inches"
}
```
