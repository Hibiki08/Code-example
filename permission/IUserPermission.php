<?php

namespace common\components\permission;

use common\models\User;

/**
 * Interface IUserPermission
 * @package common\components\permission
 */
interface IUserPermission
{
    /**
     * Возвращает роль, которая имеет доступ к данному разрешению
     *
     * @return string
     */
    public function allowedRole(): string;

    /**
     * Определяет доступ на чтение к пользователю
     *
     * @param User $targetUser
     * @return bool
     */
    public function canAccess(User $targetUser): bool;

    /**
     * Определяет доступ на изменение по пользователю
     *
     * @param User $targetUser
     * @return bool
     */
    public function canChange(User $targetUser): bool;

    /**
     * Определяет доступ по пермишену компании
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool;
}