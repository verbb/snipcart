# Testing Webhooks

- Use a client like [Insomnia](https://insomnia.rest/) to feed sample order JSON to your site's local webhook endpoint.

## Health Check
You can optionally send a POST request to /actions/snipcart/test/check-health to verify that the Snipcart plugin is installed and capable of handling webhook requests. If all's well, the response will look like this:

```json
{
  "status": "healthy"
}
```

:::tip
Configure uptime tools like Pingdom, UptimeRobot and StatusCake to send a post check and verify a successful response.
:::