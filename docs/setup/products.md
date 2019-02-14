---
meta:
  - name: description
    content: Defining Snipcart products in Craft CMS.
---

# Define Product Details

The easiest way to turn Entries into Products is to add the included _Product Details_ field to whatever field layout you'd like. This comes with a few benefits:

- A reasonably compact field type for storing typical product information: SKU, price, inventory, taxable and shippable status, weight and dimensions.
- The ability to switch on a plugin setting to automatically decrease product inventory as orders come in.
- Convenient, flexible [template method for outputting _Buy Now_ buttons](/templating/fields.md), which contain the critical details that define products for Snipcart.

But you don't have to use this field type at all.

You can write your own markup and use whatever [Events](/dev/events.md) you'd like to control exactly how your site interacts with Snipcart.