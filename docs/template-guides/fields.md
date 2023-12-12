# Fields
_Add to Cart_ buttons are critical to Snipcart because they define products behind the scenes. If you've used the _Product Details_ field, there's a `getBuyNowButton()` method that will surely save you some time. By default it will...

- Include a product's SKU, name, price, URL, quantity (of 1), and taxable+shippable status.
- Include weight and dimensions (in grams and centimeters) if the item is shippable.

It will also simplify the process of adding custom variations (color, size, etc.) whether or not they affect the product price.

For these examples, we'll assume your Snipcart Product Details field has the handle `productDetails`.

## Buy Button
The quickest way to add a product add-to-cart button.

```twig
{{ entry.productDetails.getBuyNowButton() }}
```

The default markup will look something like this without any [customization](docs:templating/fields#additional-options):

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

## Custom Attributes
You can also pass an object of attribute key-values, which will set any HTML attribute on the anchor tag.

```twig
{{ entry.productDetails.getBuyNowButton({
    rel: 'some-rel',
    href: '#add-to-cart',
    class: ['btn'],
}) }}

<a href="#add-to-cart"
    rel="some-rel"
    class="snipcart-add-item btn"
    data-item-id="to-slay-a-mockingbird"
    data-item-name="To Slay a Mockingbird"
    data-item-price="12.99"
    data-item-url="https://craftcms.dev/products/to-slay-mockingbird"
>Buy Now</a>
```

## Buy Button + Simple Options
Optionally supply custom options that don't affect pricing.

```twig
{{ entry.productDetails.getBuyNowButton({
    customOptions: [{
        name: 'Size',
        required: true,
        options: ['Small', 'Medium', 'Large'],
    }]
}) }}
```

## Buy Button + Price-Variant Options
Custom options that each adjust the base product price. Each amount can be positive (increasing product price), negative (reducing product price), or zero (not affecting product price).

```twig
{{ entry.productDetails.getBuyNowButton({
    customOptions: [{
        name: 'Bling Type',
        required: true,
        options: [
            {
                name: 'Bedazzled',
                price: 0,
            },
            {
                name: 'Bronzed',
                price: 25,
            },
            {
                name: 'Diamond Studded',
                price: 500,
            },
            {
                name: 'Used, Bad Shape',
                price: -50,
            },
        ],
    }],
}) }}
```

## Buy Button + Price Override
If you have a specific need to override the item's price in your template, passing a `price` property will do exactly that:

```twig
{{ entry.productDetails.getBuyNowButton({
    price: 100,
}) }}
```

A key/value JSON array can also be used to define prices in different currencies, provided that you've [configured your store](https://docs.snipcart.com/v3/configuration/multi-currency) to support multiple currencies.

```twig
{{ entry.productDetails.getBuyNowButton({
    price: { 'usd': 20, 'cad': 26.84 },
}) }}
```

## All Options

| Property          | Requires                   | Description                                                                                                                                                                                              |
| ----------------- | -------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `classes`         | array                      | Array of additional class names to be added to the anchor element.<br><br>`.snipcart-add-item` will automatically be added and cannot be removed since it's functionally required. |
| `text`            | string                     | Inner text, which defaults to `Buy Now`.                                         
| `quantity`        | integer                    | Initial quantity to be added to the cart. Defaults to `1`.                                                                                                                                               |
| `image`           | string                     | URL for a product thumbnail to be used in the cart. The default cart template's image is 50px square.                                                                                                    |
| `price`           | decimal or key/value array | Price override, or key/value array to define multiple currencies (`{ 'usd': 20, 'eur': 17.79 }`). Defaults to the price defined in the Product Details field.                                            |

## Querying Elements by Product Details
You can query elements by information stored in the Product Details field. For example, you can grab `products` entries that have inventory:

```twig
{% set availableProducts = craft.entries()
    .section('products')
    .productDetails({ inventory: '> 0' })
    .all() %}
```

Or get items that are not shippable:

```twig
{% set availableProducts = craft.entries()
    .section('products')
    .productDetails({ shippable: false })
    .all() %}
```

Or all items more than $50 but less than $1,000:

```twig
{% set availableProducts = craft.entries()
    .section('products')
    .productDetails({ price: '> 50', price: '< 1000' })
    .all() %}
```