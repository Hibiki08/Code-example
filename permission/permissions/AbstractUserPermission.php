<?php

namespace common\components\permission\permissions;

use common\components\permission\IUserPermission;
use common\models\User;
use yii\base\BaseObject;

/**
 * Class AbstractUserPermission
 * @package common\components\permission
 */
abstract class AbstractUserPermission extends BaseObject implements IUserPermission
{
    /** @var User */
    private $user;

    /** @inheritdoc */
    public function __construct(User $user, array $config = [])
    {
        parent::__construct($config);
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $targetUser
     * @return bool
     */
    public function canAccess(User $targetUser): bool
    {
        if ($this->user->role !== $this->allowedRole()) {
            return false;
        }

        if ($this->isSelf($targetUser)) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function isSelf(User $user): bool
    {
        return $this->user->id === $user->id;
    }

    /**
     * @param User $user
     * @return bool
     */
    protected function isUserFromSameCompany(User $user): bool
    {
        return $this->user->company_id === $user->company_id;
    }

    /**
     * @param User $targetUser
     * @return bool
     */
    public function canChange(User $targetUser): bool
    {
        return static::canAccess($targetUser);
    }

    /**
     * Определяет, имеет ли пользователь право на выполнение действия
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        try {
            $companyPermissions = $this->user->company->permissions;
            $availableForAccessRoles = array_slice(User::ROLES, 0, $companyPermissions->$permission);
            return in_array($this->user->role, $availableForAccessRoles);
        } catch (\Exception $e) {
            return false;
        }
    }
}