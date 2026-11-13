# Queries

Snipcart doesn’t add its own top-level GraphQL queries. Instead, you query the element that holds a [Product Details field](docs:feature-tour/products) through Craft’s own queries, such as `entries`, and select the product details you need. GraphQL requires Craft Pro.

## Prepare a Schema

In Craft’s **GraphQL → Schemas**, create or edit a schema that can read the section holding your products and the relevant site. Use **GraphQL → GraphiQL** with that schema to try a query. Craft’s [GraphQL documentation](https://craftcms.com/docs/5.x/development/graphql.html) explains schema permissions, tokens and endpoint configuration.

## Query Product Details

This example assumes a Products section with the handle `products`, an entry type with the handle `product`, and a Product Details field with the handle `productDetails`. Replace these handles with your site’s values.

```graphql
{
  entries(section: "products", limit: 1) {
    ... on product_Entry {
      title
      productDetails {
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
    }
  }
}
```

The fragment `product_Entry` identifies the entry type containing the field. The response looks like this.

```json
{
  "data": {
    "entries": [
      {
        "title": "Infinity Gauntlet",
        "productDetails": {
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
      }
    ]
  }
}
```

[Interfaces & Types](docs:graphql/interfaces-types) lists every field you can select.
