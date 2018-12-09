<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models;

use Craft;
use craft\base\Model;

/**
 * https://docs.snipcart.com/api-reference/products
 */

class SnipcartProduct extends Model
{
  // Properties
  // =========================================================================

  public $id;
    public $creationDate;
    public $modificationDate;
    public $mode;
    public $userDefinedId;
    public $url;
    public $price;
    public $name;
    public $description;
    public $image;
    public $archived;
    public $statistics;
        // numberOfSales
        // totalSales
    public $customFields;
    public $metaData;
    public $inventoryManagementMethod;
    public $stock;
    public $totalStock;
    public $allowOutOfStockPurchases;
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
}
