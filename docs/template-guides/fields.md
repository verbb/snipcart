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

The default markup will look something like this without any [customization](docs:template-guides/fields#all-options):

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
Alongside the [documented options](docs:template-guides/fields#all-options) (`price`, `classes`, `customOptions`, etc.), you can pass any other key/value pairs. Each becomes an HTML attribute on the same `<a>` as the buy button—so Snipcart reads `data-item-*` attributes you add here on the real add-to-cart control (unlike a second manual `<a>` elsewhere on the page).

The plugin always outputs Snipcart's standard product attributes from the Product Details field (`data-item-id`, `data-item-name`, `data-item-price`, and the rest). The HTML samples below are **abbreviated on purpose**: they only show the attributes you pass in Twig, so it is obvious what *you* control. In view source, the anchor will still include every default `data-item-*` (and dimensions/weight when shippable), as in the [default markup](docs:template-guides/fields#buy-button) example above.

### Extra HTML attributes

```twig
{{ entry.productDetails.getBuyNowButton({
    rel: 'nofollow',
    href: '#add-to-cart',
    classes: ['btn', 'btn-primary'],
}) }}
```

Abbreviated resulting anchor (all default Snipcart `data-item-*` attributes omitted for clarity):

```html
<a href="#add-to-cart"
    rel="nofollow"
    class="snipcart-add-item btn btn-primary"
>Buy Now</a>
```

### Snipcart `data-item-*` attributes

Use the same object for attributes [documented by Snipcart](https://docs.snipcart.com/v3/setup/products#advanced-product-attributes) that are not filled in by the field—for example categories (pipe-separated) for global category discounts:

```twig
{{ entry.productDetails.getBuyNowButton({
    'data-item-categories': 'doors|windows',
}) }}
```

Other common examples:

```twig
{{ entry.productDetails.getBuyNowButton({
    'data-item-metadata': { internalRef: '123' }|json_encode,
    'aria-label': 'Add ' ~ entry.title ~ ' to cart',
}) }}
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
| `name`            | string                     | The name of the product.                                         
| `url`             | string                     | The URL to the product page.                                         
| _(any other key)_ | mixed                      | Treated as an HTML attribute on the anchor (for example `rel`, `aria-label`, or Snipcart values such as `data-item-categories`). See [Custom attributes](docs:template-guides/fields#custom-attributes). |


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