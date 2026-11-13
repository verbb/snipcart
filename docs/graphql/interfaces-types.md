# Interfaces & Types

Each Product Details field has its own GraphQL type, named after the field’s handle. [Queries](docs:graphql/queries) shows how to request the field through Craft’s entry queries and set up the schema.

## The `[fieldHandle]_SnipcartField` Type

A Product Details field with the handle `productDetails` returns the `productDetails_SnipcartField` type. Select any of these fields on it. All of them are nullable.

```graphql
{
  entries(section: "products", limit: 1) {
    ... on product_Entry {
      productDetails {
        sku
        price
        inventory
      }
    }
  }
}
```

::: reference
### `sku`

**Type:** `String`

The SKU of this product, which Snipcart uses as the item’s ID. This is `null` when no SKU is set.
:::

::: reference
### `price`

**Type:** `Float`

The price of this product. This is `null` when no price is set.
:::

::: reference
### `shippable`

**Type:** `Boolean`

Whether this product is shippable.
:::

::: reference
### `taxable`

**Type:** `Boolean`

Whether this product is taxable.
:::

::: reference
### `weight`

**Type:** `Float`

The weight of this product, in the unit given by `weightUnit`. This is `null` when no weight is set.
:::

::: reference
### `weightUnit`

**Type:** `String`

The unit for `weight`, which is `grams`, `ounces` or `pounds`.
:::

::: reference
### `length`

**Type:** `Float`

The length of this product, in the unit given by `dimensionsUnit`. This is `null` when no length is set.
:::

::: reference
### `width`

**Type:** `Float`

The width of this product, in the unit given by `dimensionsUnit`. This is `null` when no width is set.
:::

::: reference
### `height`

**Type:** `Float`

The height of this product, in the unit given by `dimensionsUnit`. This is `null` when no height is set.
:::

::: reference
### `inventory`

**Type:** `Int`

The number of this product in stock. This is `null` when no inventory is set.
:::

::: reference
### `dimensionsUnit`

**Type:** `String`

The unit for `length`, `width` and `height`, which is `centimeters` or `inches`.
:::
