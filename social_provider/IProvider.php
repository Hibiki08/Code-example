<?php

namespace api3\components\social_provider;

/**
 * Интерфейс для провайдеров авторизации через социальные сети
 *
 * Interface IProvider
 * @package api3\components
 */
interface IProvider
{
    /**
     * @return string
     */
    public function getBaseUrl(): string;

    /**
     * Возвращает декодированный ответ от социальной сети
     *
     * @return void
     */
    public function decodeContent(): void;

    /**
     * Возвращает общие данные пользователя
     *
     * @return mixed|null
     */
    public function getDecodedContent();

    /**
     * @return string|null
     */
    public function getEmail(): ?string;
}