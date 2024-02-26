<?php

namespace api3\components\social_provider;

use api3\components\CustomASDecoder;
use api3\components\CustomASPayload;
use Exception;

/**
 * Apple провайдер
 *
 * class AppleProvider
 * @package api3\components\social_provider
 */
class AppleProvider extends AbstractSocialProvider
{
    /** @var string */
    private $jwt;

    /** @var string */
    private $audience;

    /** @var CustomASPayload */
    protected $decodedContent;

    /** @inheritdoc
     * @throws Exception
     */
    public function __construct(string $jwt, array $config = [])
    {
        $this->jwt = $jwt;
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function getEmail(): ?string
    {
        if ($this->decodedContent->isEmailVerified()) {
            return $this->decodedContent->getEmail();
        }
        return null;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function decodeContent(): void
    {
        $appleSignInPayload = CustomASDecoder::getAppleSignInPayload($this->jwt);

        if (!$appleSignInPayload->verifyAudience($this->audience)) {
            throw new Exception('Invalid audience');
        }

        if (!$appleSignInPayload->verifyIssuer()) {
            throw new Exception('Invalid issuer');
        }

        $this->decodedContent = $appleSignInPayload;
    }

    /**
     * @param string $audience
     */
    public function setAudience(string $audience): void
    {
        $this->audience = $audience;
    }
}