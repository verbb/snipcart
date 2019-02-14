# Testing Webhooks

- Use a client like [Insomnia](https://insomnia.rest/) to feed sample order JSON to your site's local webhook endpoint.
- Use the [testing/demo site](https://github.com/workingconcept/snipcart-test) to run automated tests and/or poke at webhooks in a safe environment. All email there is captured with [Mailhog](https://github.com/mailhog/MailHog), which provides a web GUI for inspecting messages that would have gone out into the wild.