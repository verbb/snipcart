# Templating
If there's one thing you need to know, it's that **Snipcart's definition of store products comes from information embedded in buy buttons.**

This means you can build up your Craft site and Elements however you'd like. Just make sure your buy buttons send exactly the details you want into the cart.

Start with [Snipcart's installation guide](https://docs.snipcart.com/v3/) if you'd prefer to write your own markup, or use the following template tags to have it generated for you.

## Cart JavaScript
Add Snipcart's JavaScript snippet to allow your site to interact with the remote Snipcart shipping cart.

```twig
{{ craft.snipcart.cartJs() }}
```

You can also pass in any options defined in the [Snipcart docs](https://docs.snipcart.com/v3/).

```twig
{{ craft.snipcart.cartJs({
    loadStrategy: 'manual',
}) }}
```

This will add the JavaScript to the end of the page, near the `</body>` tag, but you can also render it inline.

```twig
{{ craft.snipcart.cartJs({}, false) }}
```

## Cart Button
Add a link that'll include the item count and pop open the cart modal when clicked:

```twig
{{ craft.snipcart.cartLink() }}
```

## Buy Button
Buy buttons use data attributes to define Snipcart products. The included _Product Details_ field type provides a highly configurable way of establishing these buttons.

The simplest version looks like this, and you'd add it to product detail pages or wherever you'd like to let a visitor add a product to the cart:

```twig
{# Buy Now #}
{{ entry.productDetails.getBuyNowButton() }}
```

More on customizing these buttons [here](docs:templating/fields).