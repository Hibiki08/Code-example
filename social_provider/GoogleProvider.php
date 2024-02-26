<?php

namespace api3\components\social_provider;

use common\exceptions\ValidateExceptionV3;
use Exception;

/**
 * Google провайдер
 *
 * class GoogleProvider
 * @package api3\components\social_provider
 */
class GoogleProvider extends AbstractSocialProvider
{
    /** @var string */
    private $jwt;

    /**
     * @param string $jwt
     * @param array $config
     * @throws ValidateExceptionV3
     */
    public function __construct(string $jwt, array $config = [])
    {
        $this->jwt = $jwt;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function decodeContent(): void
    {
        $url = $this->createUrl('tokeninfo', ['id_token' => $this->jwt]);
        $decodedContent = $this->execute($url);

        if (isset($decodedContent['error'])) {
            throw new Exception(self::TOKEN_ERROR_MESSAGE);
        }

        $this->decodedContent = $decodedContent;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        if ($this->decodedContent['email_verified']) {
            return $this->decodedContent['email'];
        }
        return null;
    }
}