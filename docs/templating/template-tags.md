---
meta:
  - name: description
    content: Snipcart plugin template tag reference.
---

 Template Tags

## Cart Snippet

Use `cartSnippet(false)` if you've already included jQuery:

```twig
{# include Snipcart JS #}
{{ craft.snipcart.cartSnippet }}
```

## Cart Button

```twig
{# View Cart #}
{{ craft.snipcart.cartLink }}
```
