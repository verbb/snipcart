<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\snipcart;

/**
 * https://docs.snipcart.com/v2/api-reference/products
 */

class Product extends \craft\base\Model
{
    /**
     * @var
     */
    public $id;

    /**
     * @var \DateTime
     */
    public $creationDate;

    /**
     * @var \DateTime
     */
    public $modificationDate;

    /**
     * @var
     */
    public $mode;

    /**
     * @var
     */
    public $userDefinedId;

    /**
     * @var
     */
    public $url;

    /**
     * @var
     */
    public $price;

    /**
     * @var
     */
    public $name;

    /**
     * @var
     */
    public $description;

    /**
     * @var
     */
    public $image;

    /**
     * @var
     */
    public $archived;

    /**
     * @var
     */
    public $statistics;
        // numberOfSales
        // totalSales

    /**
     * @var
     */
    public $customFields;

    /**
     * @var
     */
    public $metaData;

    /**
     * @var
     */
    public $inventoryManagementMethod;

    /**
     * @var
     */
    public $stock;

    /**
     * @var
     */
    public $totalStock;

    /**
     * @var
     */
    public $allowOutOfStockPurchases;

    /**
     * @var
     */
    public $variants;

    /*

    "items": [


       {
        "variants": [
          {
            "stock": 10,
            "variation": [
              {
                "name": "Size",
                "option": "16GB"
              },
              {
                "name": "Color",
                "option": "Black"
              }
            ],
            "allowOutOfStockPurchases": true
          },
          {
            "stock": 1,
            "variation": [
              {
                "name": "Size",
                "option": "32GB"
              },
              {
                "name": "Color",
                "option": "Red"
              }
            ],
            "allowOutOfStockPurchases": false
          }
        ],
        "metadata": {
            "meta": true
        },
        "id": "3932ecd1-6508-4209-a7c6-8da4cc75590d",
        "creationDate": "2016-11-03T12:51:04.297Z",
        "modificationDate": "2016-11-03T12:51:28.873Z"
      }
    ]
      */

    /**
     * @inheritdoc
     */
    public function datetimeAttributes(): array
    {
        return ['creationDate', 'modificationDate'];
    }

}
