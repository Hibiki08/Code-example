<?php

namespace api3\components\social_provider;

use common\exceptions\ValidateExceptionV3;
use Exception;
use yii\base\BaseObject;

/**
 * Абстрактный класс для провайдеров авторизации через социальные сети
 *
 * class AbstractSocialProvider
 * @package api3\components\social_provider
 */
abstract class AbstractSocialProvider extends BaseObject implements IProvider
{
    /** @var string */
    protected $baseUrl;

    /** @var array */
    protected $decodedContent;

    /** @var string */
    protected const TOKEN_ERROR_MESSAGE = 'Token is not valid or expired';

    /**
     * @inheritdoc
     * @throws ValidateExceptionV3
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        try {
            $this->decodeContent();
        } catch (Exception $e) {
            throw new ValidateExceptionV3($e->getMessage());
        }
    }

    /**
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return array
     */
    protected function executePost(string $url, array $params = [], array $headers = []): array
    {
        return $this->execute($url, $headers, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_ENCODING => "",
        ]);
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @param string $url
     * @param array $headers
     * @param array $extraOptions
     * @return array
     */
    protected function execute(string $url, array $headers = [], array $extraOptions = []): array
    {
        $connection = curl_init();

        curl_setopt_array($connection, array_replace([
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
        ], $extraOptions));

        $response = curl_exec($connection);

        curl_close($connection);

        return json_decode($response, true);
    }

    /**
     * @param string $path
     * @param array $params
     * @param string|null $url
     * @return string
     */
    protected function createUrl(string $path, array $params = [], string $url = null): string
    {
        $baseUrl = $url ?? $this->getBaseUrl();
        $url = $baseUrl . $path;
        if ($params) {
            $url .= '?';
            foreach ($params as $key => $value) {
                $url .= $key . '=' . $value . '&';
            }
        }
        return rtrim($url, '&');
    }

    /**
     * @return array
     */
    public function getDecodedContent(): array
    {
        return $this->decodedContent;
    }
}