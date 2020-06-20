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
    /**
     * Gets number of orders, by date, between two dates.
     *
     * @param int|\DateTime $from Beginning of date range as Unix timestamp
     *                            or DateTime
     * @param int|\DateTime $to   End of date range as Unix timestamp
     *                            or DateTime
     *
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
                'from' => $this->prepDate($from),
                'to'   => $this->prepDate($to),
            ]
        );
    }

    /**
     * Gets store performance statistics between two dates.
     *
     * @param int|\DateTime $from Beginning of date range as Unix timestamp
     *                            or DateTime
     * @param int|\DateTime $to   End of date range as Unix timestamp
     *                            or DateTime
     *
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
                'from' => $this->prepDate($from),
                'to'   => $this->prepDate($to),
            ]
        );
    }

    /**
     * Gets store sales totals between to dates.
     *
     * @param int|\DateTime $from Beginning of date range as Unix timestamp
     *                            or DateTime
     * @param int|\DateTime $to   End of date range as Unix timestamp
     *                            or DateTime
     *
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
                'from' => $this->prepDate($from),
                'to'   => $this->prepDate($to),
            ]
        );
    }

    /**
     * Takes a timestamp or DateTime instance and returns a Unix timestamp
     * string for the REST API request.
     *
     * @param int|\DateTime $date
     * @return string
     */
    private function prepDate($date): string
    {
        if ($date instanceof \DateTime) {
            return $date->format('U');
        }

        return (string) $date;
    }
}