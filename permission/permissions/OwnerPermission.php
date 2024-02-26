<?php

namespace common\components\permission\permissions;

use common\models\User;

/**
 * Class OwnerPermission
 * @package common\components\permission
 */
class OwnerPermission extends AbstractUserPermission
{
    /**
     * @inheritDoc
     */
    public function allowedRole(): string
    {
        return User::ROLE_OWNER;
    }

    /** @inheritdoc */
    public function canAccess(User $targetUser): bool
    {
        if (parent::canAccess($targetUser)) {
            return true;
        }

        return $targetUser->isActive() && $this->isUserFromSameCompany($targetUser);
    }
}