---
meta:
  - name: description
    content: Testing webhooks with the Snipcart Craft CMS plugin.
---

# Testing Webhooks

- Use a client like [Insomnia](https://insomnia.rest/) to feed sample order JSON to your site's local webhook endpoint.
- Use the [testing/demo site](https://github.com/workingconcept/snipcart-test) to run automated tests and/or poke at webhooks in a safe environment. All email there is captured with [Mailhog](https://github.com/mailhog/MailHog), which provides a web GUI for inspecting messages that would have gone out into the wild.

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