<?php

namespace common\components\permission\permissions;

use common\models\User;

/**
 * Class AdminPermission
 * @package common\components\permission
 */
class AdminPermission extends AbstractUserPermission
{
    /**
     * @inheritDoc
     */
    public function allowedRole(): string
    {
        return User::ROLE_ADMIN;
    }

    /** @inheritdoc */
    public function canAccess(User $targetUser): bool
    {
        if (parent::canAccess($targetUser)) {
            return true;
        }

        if ($targetUser->isUser() || $targetUser->isManager()) {
            return $targetUser->isActive() && $this->isUserFromSameCompany($targetUser);
        }

        return false;
    }
}