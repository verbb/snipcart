# Test Mode
Snipcart accounts have two modes: the live one your store should use in production, and a test mode that's completely isolated for learning, experimenting, and testing without taking payments or impacting merchant operations. It's like having two separate Snipcart accounts, but more convenient switching back and forth.

The Snipcart plugin can be configured to operate in either mode. Once you've added both sets of API keys, the _Test Mode_ setting will make it easy to switch between live and test mode.

There are a few things to be aware of when you're working with the Snipcart plugin in test mode:

-   All data will be isolated just like it's a separate Snipcart account.
-   Payment gateway interaction will be disabled, and fake credit card numbers can be used to test successul transactions.
-   The Snipcart Craft plugin will not send any emails, unless you have that setting turned on.
-   Completed orders will not be forwarded to ShipStation.
-   You'll probably want to add a testing domain and webhook to Snipcart, which can be separate for test mode. (This is a great place to use [ngrok](https://ngrok.com/)!)
