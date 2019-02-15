---
meta:
    - name: description
      content: How to add Snipcart product purchase buttons to your Craft CMS frontend.
---

# Product Details

_Add to Cart_ buttons are critical to Snipcart because they define products behind the scenes. If you've used the _Product Details_ field, there's a `getBuyNowButton()` method that will surely save you some time. By default it will...

-   Include a product's SKU, name, price, URL, quantity (of 1), and taxable+shippable status.
-   Include weight and dimensions (in grams and centimeters) if the item is shippable.

It will also simplify the process of adding custom variations (color, size, etc.) whether or not they affect the product price.

## Buy Button

Simplest form.

```twig
{# Buy Now #}
{{ entry.productDetails.getBuyNowButton() | raw }}
```

## Buy Button + Simple Options

Optionally supply custom options that don't affect pricing.

```twig
{# Buy Now button with custom options #}
{{ entry.productDetails.getBuyNowButton({
    customOptions: [
        {
            name: 'Size',
            required: true,
            options: [ 'Small', 'Medium', 'Large' ]
        }
    ]
}) | raw }}
```

## Buy Button + Price-Variant Options

Custom options that each add different amounts to the base product price.

```twig
{{ entry.productDetails.getBuyNowButton({
    customOptions: [
       {
           name: 'Bling Type',
           required: true,
           options: [
                 {
                     name: 'Bedazzled',
                     price: 5
                 },
                 {
                     name: 'Bronzed',
                     price: 25
                 },
                 {
                     name: 'Diamond Studded'
                     price: 500
                 }
            ]
       }
   ]
}) | raw }}
```

## Additional Options

Because you'll want to customize that button, and why shouldn't you?

| Property          | Requires             | Description                                                                                                                                                     |
| ----------------- | -------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `classes`         | array `[]`           | Array of class names to be added to the anchor element.                                                                                                         |
| `text`            | string `''`          | Inner text, which defaults to `Buy Now`.                                                                                                                        |
| `target`          | string `''`          | Anchor [target](https://www.w3schools.com/TAGs/att_a_target.asp).                                                                                               |
| `title`           | string `''`          | Anchor [title](https://www.w3schools.com/tags/att_title.asp).                                                                                                   |
| `rel`             | string `''`          | Anchor [relationship](https://www.w3schools.com/TAGs/att_a_rel.asp).                                                                                            |
| `additionalProps` | key/value array `''` | Attribute+value pairs for the anchor. Useful for supplying additional [product definition](https://docs.snipcart.com/configuration/product-definition) details. |
