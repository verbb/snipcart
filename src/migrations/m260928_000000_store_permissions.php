<?php
namespace verbb\snipcart\migrations;

use verbb\snipcart\Snipcart;

use Craft;
use craft\db\Migration;
use craft\db\Query;
use craft\db\Table;

use Throwable;

class m260928_000000_store_permissions extends Migration
{
    // Constants
    // =========================================================================

    private const LEGACY_PERMISSION = 'accessplugin-snipcart';
    private const STORE_PERMISSIONS = [
        Snipcart::PERMISSION_VIEW_STORE,
        Snipcart::PERMISSION_REFUND_ORDERS,
        Snipcart::PERMISSION_MANAGE_DISCOUNTS,
        Snipcart::PERMISSION_MANAGE_SUBSCRIPTIONS,
    ];


    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->_backfillGroups();
        $this->_backfillUsers();

        return true;
    }

    public function safeDown(): bool
    {
        echo "m260928_000000_store_permissions cannot be reverted.\n";

        return false;
    }


    // Private Methods
    // =========================================================================

    private function _backfillGroups(): void
    {
        $userPermissions = Craft::$app->getUserPermissions();

        foreach (Craft::$app->getUserGroups()->getAllGroups() as $group) {
            $existing = $userPermissions->getPermissionsByGroupId($group->id);
            $granted = $this->_addStorePermissions($existing);

            if ($granted === $existing) {
                continue;
            }

            try {
                $userPermissions->saveGroupPermissions($group->id, $granted);
            } catch (Throwable $e) {
                Craft::warning("Couldn’t backfill Snipcart permissions for user group “{$group->handle}”: {$e->getMessage()}", __METHOD__);
            }
        }
    }

    private function _backfillUsers(): void
    {
        $userPermissions = Craft::$app->getUserPermissions();
        $userIds = (new Query())
            ->select(['p_u.userId'])
            ->distinct()
            ->from(['p_u' => Table::USERPERMISSIONS_USERS])
            ->innerJoin(['p' => Table::USERPERMISSIONS], '[[p.id]] = [[p_u.permissionId]]')
            ->where(['p.name' => self::LEGACY_PERMISSION])
            ->column();

        foreach ($userIds as $userId) {
            $existing = (new Query())
                ->select(['p.name'])
                ->from(['p_u' => Table::USERPERMISSIONS_USERS])
                ->innerJoin(['p' => Table::USERPERMISSIONS], '[[p.id]] = [[p_u.permissionId]]')
                ->where(['p_u.userId' => $userId])
                ->column();

            try {
                $userPermissions->saveUserPermissions((int)$userId, $this->_addStorePermissions($existing));
            } catch (Throwable $e) {
                Craft::warning("Couldn’t backfill Snipcart permissions for user {$userId}: {$e->getMessage()}", __METHOD__);
            }
        }
    }

    private function _addStorePermissions(array $permissions): array
    {
        $lowered = array_map('strtolower', $permissions);

        if (!in_array(self::LEGACY_PERMISSION, $lowered, true)) {
            return $permissions;
        }

        foreach (self::STORE_PERMISSIONS as $permission) {
            if (!in_array(strtolower($permission), $lowered, true)) {
                $permissions[] = $permission;
            }
        }

        return $permissions;
    }
}
