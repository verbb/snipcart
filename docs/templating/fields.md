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

:::tip
Working with your own markup? The [Snipcart plugin's internal Twig template](https://github.com/workingconcept/snipcart-craft-plugin/blob/master/src/templates/fields/front-end/buy-now.twig) may provide a helpful starting point.
:::

## Buy Button

Simplest form.

```twig
{# Buy Now #}
{{ entry.productDetails.getBuyNowButton() | raw }}
```

The default markup will look something like this without any [customization](/templating/fields.md#additional-options):

```html
<a href="#"
    class="snipcart-add-item"
    data-item-id="to-slay-a-mockingbird"
    data-item-name="To Slay a Mockingbird"
    data-item-price="12.99"
    data-item-url="https://craftcms.dev/products/to-slay-mockingbird"
    data-item-quantity="1"
    data-item-taxable="false"
    data-item-shippable="true"
    data-item-width="13"
    data-item-length="21"
    data-item-height="3"
    data-item-weight="3"
>Buy Now</a>
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

:::tip
While you can hardcode these values just like the examples, they could just as well come from another Entry field, like a [Table](https://docs.craftcms.com/v3/table-fields.html#settings) or a [Matrix Block](https://docs.craftcms.com/v3/matrix-fields.html#settings) that you've established for product variations. It's up to you!
:::

## Buy Button + Price-Variant Options

Custom options that each adjust the base product price. Each amount can be positive (increasing product price), negative (reducing product price), or zero (not affecting product price).

```twig
{{ entry.productDetails.getBuyNowButton({
    customOptions: [
       {
           name: 'Bling Type',
           required: true,
           options: [
                 {
                     name: 'Bedazzled',
                     price: 0
                 },
                 {
                     name: 'Bronzed',
                     price: 25
                 },
                 {
                     name: 'Diamond Studded',
                     price: 500
                 },
                 {
                     name: 'Used, Bad Shape',
                     price: -50
                 }
            ]
       }
   ]
}) | raw }}
```

## Buy Button + Price Override

If you have a specific need to override the item's price in your template, passing a `price` property will do exactly that:

```twig
{{ entry.productDetails.getBuyNowButton({
    price: 100
}) | raw }}
```

A key/value JSON array can also be used to define prices in different currencies, provided that you've [configured your store](https://docs.snipcart.com/configuration/multi-currency) to support multiple currencies.

```twig
{{ entry.productDetails.getBuyNowButton({
    price: {"usd": 20, "cad": 25}
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
