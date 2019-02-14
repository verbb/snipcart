<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\services;

use workingconcept\snipcart\Snipcart;

/**
 * Undocumented data API methods for grabbing store statistics.
 *
 * @package workingconcept\snipcart\services
 */
class Data extends \craft\base\Component
{
    // Public Methods
    // =========================================================================

    /**
     * Get number of orders for a date range.
     *
     * @param $from
     * @param $to
     * @return array|\stdClass|null
     *
     * Return example:
     * ```
     * {
     *   "labels": [
     *     "Number of orders"
     *   ],
     *   "data": [
     *     {
     *       "name": "2017-04-04",
     *       "value": 12
     *     },
     *     ...
     *   ],
     *   "to": 1493881200,
     *   "from": 1491289200,
     *   "currency": null
     * }
     * ```
     *
     * @throws \Exception
     */
    public function getOrderCount($from, $to)
    {
        return Snipcart::$plugin->api->get(
            'data/orders/count',
            [
                'from' => $this->_prepDate($from),
                'to'   => $this->_prepDate($to),
            ]
        );
    }

    /**
     * Get store performance statistics.
     *
     * @param $from
     * @param $to
     * @return array|\stdClass|null
     *
     * Return example:
     * ```
     * {
     *   "ordersSales": 100.00,
     *   "ordersCount": 10,
     *   "averageCustomerValue": 10.000000,
     *   "taxesCollected": 10.00,
     *   "shippingCollected": 10.00,
     *   "customers": {
     *     "newCustomers": 10,
     *     "returningCustomers": 10
     *   },
     *   "averageOrdersValue": 0.000000000000000000000000000,
     *   "totalRecovered": 0.0
     * }
     * ```
     * @throws \Exception
     */
    public function getPerformance($from, $to)
    {
        return Snipcart::$plugin->api->get(
            'data/performance',
            [
                'from' => $this->_prepDate($from),
                'to'   => $this->_prepDate($to),
            ]
        );
    }

    /**
     * Get store sales totals for a date range.
     *
     * @param $from
     * @param $to
     * @return array|\stdClass|null
     *
     * Return example:
     * ```
     * {
     *   "labels": [
     *     "Total sales"
     *   ],
     *   "data": [
     *     {
     *       "name": "2017-04-04",
     *       "value": 120.13
     *     },
     *     ...
     *   ],
     *   "to": 1493881200,
     *   "from": 1491289200,
     *   "currency": null
     * }
     * ```
     *
     * @throws \Exception
     */
    public function getSales($from, $to)
    {
        return Snipcart::$plugin->api->get(
            'data/orders/sales',
            [
                'from' => $this->_prepDate($from),
                'to'   => $this->_prepDate($to),
            ]
        );
    }


    // Private Methods
    // =========================================================================

    /**
     * Make sure to pass timestamps to these API endpoints.
     *
     * @param $date
     * @return string
     */
    private function _prepDate($date): string
    {
        if ($date instanceof \DateTime)
        {
            return $date->format('U');
        }

        return $date;
    }
}