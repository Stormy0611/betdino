<?php namespace App\Permission;

use App\Role;

class Permissions {

    public static function all(): array {
        return [
            new RootPermission(),
            new ChatModeratorPermission(),
            new DashboardPermission(),
            new IgnorePrivacyPermission(),
            new WalletPermission(),
            new WithdrawsPermission(),
            new NotificationPermission(),
            new PromocodePermission(),
            new ControlUsersPermission(),
            new BannerPermission(),
            new VIPControlPermission()
        ];
    }

    public static function findById($id): ?Permission {
        foreach (self::all() as $item) {
            if($item->id() === $id) return $item;
        }

        return null;
    }

    public static function whitelistedRolesAndPermissions(array $dbRoles): array {
        $result = [];

        /** @var Permission $permission */
        foreach ($dbRoles as $dbRole) {
            $role = Role::id($dbRole['id']);
            if($role == null) continue;

            $result[] = [
                'id' => $dbRole['id'],
                'permissions' => $role->permissions
            ];
        }

        return $result;
    }

}
