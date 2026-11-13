# Template Tags

These tags add Snipcart's cart to your templates and help you show prices in your store's currency. Most sites put the cart JavaScript and cart button in their main layout, so they're on every page.

## Cart JavaScript

`craft.snipcart.cartJs()` outputs Snipcart's loader script, set up with your public API key (or your public test key while [test mode](docs:feature-tour/test-mode) is on). Add it to your layout:

```twig
{{ craft.snipcart.cartJs() }}
```

You can also pass in any options defined in the [Snipcart docs](https://docs.snipcart.com/v3/). By default, Snipcart loads the first time a visitor interacts with the page (`loadStrategy: 'on-user-interaction'`), which you can change:

```twig
{{ craft.snipcart.cartJs({
    loadStrategy: 'manual',
}) }}
```

The script is added near the closing `</body>` tag. Pass `true` as the second argument to output it where the tag is instead, such as `{{ craft.snipcart.cartJs({}, true) }}`.

## Loading Snipcart With Your Own Script

If you'd rather write Snipcart's installation snippet yourself, `craft.snipcart.publicApiKey` returns the public API key from your settings, or the public test key while test mode is on, so the snippet always uses the right one. It returns an empty string when no key is set.

```twig
{% set publicApiKey = craft.snipcart.publicApiKey %}

{% if publicApiKey %}
    <script>
        window.SnipcartSettings = {
            publicApiKey: '{{ publicApiKey | e('js') }}',
            loadStrategy: 'on-user-interaction',
        };
    </script>
    {# Paste Snipcart's loader script from its installation guide here #}
{% endif %}
```

The public key is meant to appear in the page source. Never output the secret API key in a template.

## Cart Button

`craft.snipcart.cartLink()` outputs a link that opens the cart when clicked. Like the cart JavaScript, it outputs nothing until a public API key is set.

```twig
{{ craft.snipcart.cartLink() }}
```

It takes three optional arguments, in this order:

- `text` changes the button's inner text, which defaults to “Shopping Cart”.
- `showCount` is `true` by default and includes a `<span>` element classed with `snipcart-items-count`, which Snipcart fills with the number of items in the cart.
- `showPrice` is `false` by default. Set it to `true` to include a `<span>` element classed with `snipcart-total-price`, which Snipcart fills with the cart's total.

```twig
{{ craft.snipcart.cartLink('Your Cart', true, true) }}
```

## Showing Prices in Your Store's Currency

Snipcart shows prices in the cart itself, but your product pages show them too. `craft.snipcart.getProductInfo(element)` returns the Product Details field value of any element that has one, so you don't need to know the field's handle. It returns `null` when the element's field layout has no Product Details field. `craft.snipcart.formatCurrency(value, currency)` formats an amount as currency, and `craft.snipcart.defaultCurrency` returns your store's default currency code, such as `usd`.

This example goes in a product entry's template:

```twig
{% set product = craft.snipcart.getProductInfo(entry) %}

{% if product %}
    <p class="price">{{ craft.snipcart.formatCurrency(product.price, craft.snipcart.defaultCurrency) }}</p>

    {{ product.getBuyNowButton() }}
{% endif %}
```

For a price of `12.99` in US dollars, this outputs `$12.99`, using the number format of the current site's locale.

`formatCurrency()` takes the amount and an optional currency code. Without a currency code, it uses the default currency for the current site's locale, which might not be your store's currency, so pass `craft.snipcart.defaultCurrency` to match the cart. The default currency comes from the [`defaultCurrency`](docs:get-started/configuration#defaultcurrency) setting, or the first of your [enabled currencies](docs:get-started/configuration#enabledcurrencies) when that isn't set. A string that already contains a currency symbol, such as `$12.99`, is returned as it is.

When you need the symbol on its own, for example beside a price input in a form, `craft.snipcart.defaultCurrencySymbol` returns it, such as `$`, `€`, `£` or `CHF`:

```twig
<label for="amount">Gift card amount ({{ craft.snipcart.defaultCurrencySymbol }})</label>
<input type="number" id="amount" name="amount" min="10" step="5">
```
