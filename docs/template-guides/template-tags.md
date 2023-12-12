# Template Tags

## Cart JavaScript

```twig
{{ craft.snipcart.cartJs() }}
```

You can also pass in any options defined in the [Snipcart docs](https://docs.snipcart.com/v3/).

```twig
{{ craft.snipcart.cartJs({
    loadStrategy: 'manual',
}) }}
```

## Cart Button

```twig
{{ craft.snipcart.cartLink() }}
```

- `text` can be used to change the button's inner text, which defaults to “Shopping Cart”.
- `showCount` is `true` by default and includes a `<span>` element classed with `snipcart-items-count` which Snipcart will populate with the number of items in the shopping cart.
- `showPrice` is `true` by default and includes a `<span>` element classed with `snipcart-total-price` which Snipcart will populate with the price of the shopping cart.

```twig
{{ craft.snipcart.cartLink('Shopping Cart', true, true) }}
```
