<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\variables;

use workingconcept\snipcart\models\AbandonedCart;
use workingconcept\snipcart\models\Customer;
use workingconcept\snipcart\models\Discount;
use workingconcept\snipcart\models\Order;
use workingconcept\snipcart\models\Subscription;
use workingconcept\snipcart\Snipcart;
use Craft;
use craft\helpers\Template as TemplateHelper;

class SnipcartVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @param int $pageNumber
     * @return \stdClass|array
     * @throws \Exception
     */
    public function listOrders($pageNumber = 1)
    {
        return Snipcart::$plugin->orders->listOrders($pageNumber);
    }

    /**
     * @param int $pageNumber
     * @return array
     * @throws \Exception
     */
    public function listOrdersByDay($pageNumber = 1): array
    {
        return Snipcart::$plugin->orders->listOrdersByDay($pageNumber);
    }

    /**
     * @param int $pageNumber
     * @return \stdClass|array|null
     * @throws \Exception
     */
    public function listCustomers($pageNumber = 1)
    {
        return Snipcart::$plugin->customers->listCustomers($pageNumber);
    }

    /**
     * @return \stdClass|array
     * @throws \Exception
     */
    public function listDiscounts()
    {
        return Snipcart::$plugin->discounts->listDiscounts();
    }

    /**
     * @param $discountId
     * @return Discount|null
     * @throws \Exception
     */
    public function getDiscount($discountId)
    {
        return Snipcart::$plugin->discounts->getDiscount($discountId);
    }

    /**
     * @return \stdClass|array
     * @throws \Exception
     */
    public function listAbandonedCarts()
    {
        return Snipcart::$plugin->carts->listAbandonedCarts();
    }

    /**
     * @param $token
     * @return AbandonedCart|null
     * @throws \Exception
     */
    public function getAbandonedCart($token)
    {
        return Snipcart::$plugin->carts->getAbandonedCart($token);
    }

    /**
     * @return \stdClass|array
     * @throws \Exception
     */
    public function listSubscriptions()
    {
        return Snipcart::$plugin->subscriptions->listSubscriptions();
    }

    /**
     * @param $subscriptionId
     * @return Subscription|null
     * @throws \Exception
     */
    public function getSubscription($subscriptionId)
    {
        return Snipcart::$plugin->subscriptions->getSubscription($subscriptionId);
    }

    /**
     * @param $orderId
     * @return Order|null
     * @throws \Exception
     */
    public function getOrder($orderId)
    {
        return Snipcart::$plugin->orders->getOrder($orderId);
    }

    /**
     * @param $orderId
     * @return \stdClass|array
     * @throws \Exception
     */
    public function getOrderNotifications($orderId)
    {
        return Snipcart::$plugin->orders->getOrderNotifications($orderId);
    }

    /**
     * @param $orderId
     * @return \stdClass|array
     * @throws \Exception
     */
    public function getOrderRefunds($orderId)
    {
        return Snipcart::$plugin->orders->getOrderRefunds($orderId);
    }

    /**
     * @param $customerId
     * @return Customer|null
     * @throws \Exception
     */
    public function getCustomer($customerId)
    {
        return Snipcart::$plugin->customers->getCustomer($customerId);
    }

    /**
     * @param $customerId
     * @return \stdClass|array
     * @throws \Exception
     */
    public function getCustomerOrders($customerId)
    {
        return Snipcart::$plugin->customers->getCustomerOrders($customerId);
    }

    /**
     * @return string
     */
    public function publicApiKey(): string
    {
        return Snipcart::$plugin->getSettings()->publicApiKey;
    }

    /**
     * @return bool|\DateTime
     * @throws
     */
    public function startDate()
    {
        return \DateTime::createFromFormat('U', Snipcart::$plugin->orders->dateRangeStart());
    }

    /**
     * @return bool|\DateTime
     * @throws
     */
    public function endDate()
    {
        return \DateTime::createFromFormat('U', Snipcart::$plugin->orders->dateRangeEnd());
    }

    /**
     * @return mixed|string
     * @throws
     */
    public function searchKeywords()
    {
        return Snipcart::$plugin->orders->searchKeywords();
    }

    /**
     * @param $keywords
     * @return \stdClass|array|null
     * @throws \Exception
     */
    public function searchCustomers($keywords)
    {
        return Snipcart::$plugin->customers->searchCustomers($keywords);
    }

    /**
     * @return bool
     */
    public function isLinked(): bool
    {
        return Snipcart::$plugin->api->isLinked;
    }

    /**
     * @return string
     */
    public function defaultCurrencySymbol(): string
    {
        return Snipcart::$plugin->getSettings()->getDefaultCurrencySymbol();
    }

    /**
     * Get a cart anchor with a count.
     *
     * @param string $text
     *
     * @return \Twig_Markup
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function cartLink($text = null): \Twig_Markup
    {
        return $this->_renderTemplate(
            'snipcart/front-end/cart-link',
            [ 'text' => $text ]
        );
    }

    /**
     * Get the main Snipcart JavaScript snippet, optionally including jQuery.
     *
     * @param bool $includejQuery
     * @param string $onload
     *
     * @return \Twig_Markup
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function cartSnippet($includejQuery = true, $onload = ''): \Twig_Markup
    {
        return $this->_renderTemplate(
            'snipcart/front-end/cart-js',
            [
                'settings'      => Snipcart::$plugin->getSettings(),
                'includejQuery' => $includejQuery,
                'onload'        => $onload
            ]
        );
    }

    // Private Methods
    // =========================================================================

    /**
     * @param $template
     * @param array $data
     * @return \Twig_Markup
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    private function _renderTemplate($template, $data = []): \Twig_Markup
    {
        $view         = Craft::$app->getView();
        $templateMode = $view->getTemplateMode();

        // use CP mode
        $view->setTemplateMode($view::TEMPLATE_MODE_CP);

        // render the thing
        $html = $view->renderTemplate($template, $data);

        // put it back how we found it
        $view->setTemplateMode($templateMode);

        return TemplateHelper::raw($html);
    }

}
