<?php
/**
 * Snipcart plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\snipcart\models\snipcart;

class CustomField extends \craft\base\Model
{
    /**
     * @var
     */
    public $name;

    /**
     * @var
     */
    public $operation;

    /**
     * @var
     */
    public $type;

    /**
     * @var
     */
    public $options;

    /**
     * @var
     */
    public $required;

    /**
     * @var
     */
    public $value;

    /**
     * @var
     */
    public $optionsArray;

}
