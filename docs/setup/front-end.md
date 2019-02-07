# Add Snipcart to Your Site

Snipcart's shopping cart is added with a simple JavaScript snippet, and its concept of a product comes from whatever information you build into each "buy" button on your site.

Start with [Snipcart's installation guide](https://docs.snipcart.com/getting-started/installation) if you'd prefer to write your own markup, or use the following Twig tags to have it generated for you.

## Cart Snippet

Add Snipcart's JavaScript snippet, using `cartSnippet(false)` if you've already included jQuery:

```twig
{# include Snipcart JS #}
{{ craft.snipcart.cartSnippet }}
```

## Cart Button

```twig
{# View Cart #}
{{ craft.snipcart.cartLink }}
```

## Buy Button

If you're using the included _Product Details_ field or you defined custom fields for product information (see [Configuring Products](/setup/products.md)), you can easily add _Buy_ buttons to your templates:

```twig
{# Buy Now #}
{{ entry.productDetails.getBuyNowButton() | raw }}
```

## Buy Button + Simple Options

Optionally supply custom options that don't affect pricing.

```twig
{# Buy Now button with custom options #}
{{ entry.productDetails.getBuyNowButton({
   'customOptions': [
       {
           'name': 'Color',
           'required': true,
           'options': [ 'blue', 'green', 'red', 'pink' ]
       }
   ]
}) | raw }}
```

## Buy Button + Price-Variant Options

Custom options that each add different amounts to the base product price.

```twig
{{ entry.productDetails.getBuyNowButton({
   'customOptions': [
       {
           'name': 'Color',
           'required': true,
           'options': [ 
                 {
                     'name': 'bronzed',
                     'price': 5
                 },
                 {
                     'name': 'diamond-studded'
                     'price': 500
                 }
            ]
       }
   ]
}) | raw }}

```