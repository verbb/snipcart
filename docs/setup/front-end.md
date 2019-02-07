# Adding Snipcart to Your Site

Snipcart has [an installation guide](https://docs.snipcart.com/getting-started/installation) that shows you how to get started on the front end.

You can use that if you want to add your own markup, or optionally use the following Twig tags for convenience.

## Snipcart Cart JS

Add Snipcart's JS snippet, using `cartSnippet(false)` if you've already included jQuery:

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

Set up your Craft Elements to store product details, then add _Buy_ buttons to your templates:

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