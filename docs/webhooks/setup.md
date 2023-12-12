# Setup
Get your webhook endpoint from **Settings** → **Snipcart** → **Webhooks** tab in the plugin settings and add it to the **Webhooks URL** field in [Snipcart's control panel](https://app.snipcart.com/).

That's it! Now Snipcart will post data to your Craft site as stuff happens with the store. Live webhooks will automatically be [secured and validated](https://docs.snipcart.com/v3/webhooks/introduction#secure-your-webhook-endpoint), and when `devMode` is enabled _all_ webhook posts will be allowed through.

Webhook transactions are logged under _Requests History_ in the Snipcart control panel, so you can confirm success or investigate problems there.