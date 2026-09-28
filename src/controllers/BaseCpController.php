<?php
namespace verbb\snipcart\controllers;

use verbb\snipcart\Snipcart;

use craft\web\Controller;

abstract class BaseCpController extends Controller
{
    // Properties
    // =========================================================================

    protected array $actionPermissions = [];


    // Public Methods
    // =========================================================================

    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        // These controllers expose the store's private Snipcart API through CP-only workflows.
        $this->requireCpRequest();
        $this->requirePermission('accessPlugin-snipcart');
        $this->requirePermission(Snipcart::PERMISSION_VIEW_STORE);

        if (isset($this->actionPermissions[$action->id])) {
            $this->requirePermission($this->actionPermissions[$action->id]);
        }

        return true;
    }
}
