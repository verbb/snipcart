# Permissions

Snipcart's permissions let staff work with your store from Craft without logging in to the Snipcart dashboard. Someone answering customer questions might look up orders and customers, a store manager might also issue refunds, and a marketing manager might set up discounts. Set them in **Settings → Users → User Groups**, or on an individual user's **Permissions** tab.

**Access Snipcart** is listed under **General**, with Craft's other permissions for reaching each plugin's section. The others are listed under the plugin's own heading, which shows the plugin's name in the control panel, **Snipcart** unless you've changed **Plugin Name** in its settings.

| Permission | What It Allows |
| --- | --- |
| Access Snipcart | Reach Snipcart's section of the control panel. On its own it doesn't show any store data. |
| View Snipcart store data | See the store overview, orders, customers, abandoned carts, discounts and subscriptions, and add the **Snipcart Orders** dashboard widget. |
| Refund Snipcart orders | Use **Refund Order** on an order, up to the amount that hasn't been refunded yet. |
| Manage Snipcart discounts | Create discounts with **New Discount**, and delete them. |
| Manage Snipcart subscriptions | Cancel a customer's subscription. |

Staff need both **Access Snipcart** and **View Snipcart store data** to see the store. Without the view permission, Snipcart doesn't appear in the control panel's navigation. The other three are nested under it, because each one adds an action to a screen that only the view permission opens.

Admins have every permission, and Snipcart's settings, including its API keys, are only available to admins.

Refunds and cancellations happen in your Snipcart account straight away, so keep **Refund Snipcart orders** and **Manage Snipcart subscriptions** for the people who'd make those calls in the Snipcart dashboard. A staff member with only the view permission can look up anything a customer asks about without being able to change it. The [Setup](docs:get-started/setup) guide covers giving staff access when you first set up the store.
