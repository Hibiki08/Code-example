<?php

namespace common\components\permission;

use common\components\permission\permissions\AdminPermission;
use common\components\permission\permissions\ManagerPermission;
use common\components\permission\permissions\OwnerPermission;
use common\components\permission\permissions\UserPermission;
use common\models\User;

/**
 * Class PermissionFactory
 * @package common\components\permission
 */
class PermissionFactory
{
    /**
     * @param User $user
     * @return IUserPermission
     */
    public static function createPermission(User $user): IUserPermission
    {
        switch ($user->role) {
            case User::ROLE_ADMIN:
                return new AdminPermission($user);
            case User::ROLE_OWNER:
                return new OwnerPermission($user);
            case User::ROLE_MANAGER:
                return new ManagerPermission($user);
            case User::ROLE_USER:
                return new UserPermission($user);
        }
        throw new \InvalidArgumentException('Invalid user role');
    }
}