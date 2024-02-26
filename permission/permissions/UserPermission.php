<?php

namespace common\components\permission\permissions;

use common\models\User;

/**
 * Class UserPermission
 * @package common\components\permission
 */
class UserPermission extends AbstractUserPermission
{
    /**  @inheritdoc */
    public function allowedRole(): string
    {
        return User::ROLE_USER;
    }
}