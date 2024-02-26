<?php

namespace common\components\permission\permissions;

use common\models\User;

/**
 * Class UserPermission
 * @package common\components\permission
 */
class ManagerPermission extends AbstractUserPermission
{
    /** @inheritdoc */
    public function allowedRole(): string
    {
        return User::ROLE_MANAGER;
    }

    /** @inheritdoc */
    public function canAccess(User $targetUser): bool
    {
        if (parent::canAccess($targetUser)) {
            return true;
        }

        $currentUser = $this->getUser();
        /** @var User $groupMember */
        $groupMember = User::getCompanyGroupMemberById(
            $targetUser->id,
            $currentUser->id,
            $currentUser->company_id,
        );

        return $groupMember && $groupMember->isUser();
    }
}