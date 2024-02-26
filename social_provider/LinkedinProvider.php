<?php

namespace api3\components\social_provider;

use common\exceptions\ValidateExceptionV3;
use Exception;

/**
 * Linkedin провайдер
 *
 * class LinkedinProvider
 * @package api3\components\social_provider
 */
class LinkedinProvider extends AbstractSocialProvider
{
    /** @var string */
    private $authCode;

    /** @var string */
    private $authUrl;

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var string */
    private $redirectUri;

    /**
     * @param string $authCode
     * @param array $config
     * @throws ValidateExceptionV3
     */
    public function __construct(string $authCode, array $config = [])
    {
        $this->authCode = $authCode;
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function getEmail(): ?string
    {
        if (isset($this->decodedContent['elements'])) {
            return $this->decodedContent['elements'][0]['handle~']['emailAddress'];
        }
        return null;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function decodeContent(): void
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            throw new Exception('Access token is invalid');
        }

        $url = $this->createUrl('emailAddress', [
            'q' => 'members',
            'projection' => '(elements*(handle~))',
        ]);

        $decodedContent = $this->execute($url, ['Authorization: Bearer ' . $accessToken]);

        if (isset($decodedContent['status']) && $decodedContent['status'] !== 200) {
            throw new Exception($decodedContent['message']);
        }

        $this->decodedContent = $decodedContent;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    private function getAccessToken(): ?string
    {
        $url = $this->createUrl('accessToken', [], $this->authUrl);

        $decodedContent = $this->executePost(
            $url,
            [
                'grant_type' => 'authorization_code',
                'code' => $this->authCode,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
            ],
            ['Content-Type: application/x-www-form-urlencoded']
        );

        if (isset($decodedContent['error'])) {
            throw new Exception($decodedContent['error'] . ': ' . $decodedContent['error_description']);
        }

        return $decodedContent['access_token'] ?? null;
    }

    /**
     * @param string $clientId
     */
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @param string $redirectUri
     */
    public function setRedirectUri(string $redirectUri): void
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @param string $authUrl
     */
    public function setAuthUrl(string $authUrl): void
    {
        $this->authUrl = $authUrl;
    }
}