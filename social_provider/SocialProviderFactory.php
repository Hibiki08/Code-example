<?php

namespace api3\components\social_provider;

use common\exceptions\ValidateExceptionV3;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Фабрика для создания провайдеров авторизации через социальные сети
 *
 * Class SocialProviderFactory
 * @package api3\components
 */
class SocialProviderFactory
{
    /** @var string */
    public const PROVIDER_GOOGLE = 'google';

    /** @var string */
    public const PROVIDER_APPLE = 'apple';

    /** @var string */
    public const PROVIDER_LINKEDIN = 'linkedin';

    /**
     * @param string|null $providerName
     * @param string $authToken
     * @return IProvider
     * @throws NotFoundHttpException
     * @throws ValidateExceptionV3
     */
    public function createProvider(string $providerName, string $authToken, string $action = ''): IProvider
    {
        switch ($providerName) {
            case self::PROVIDER_GOOGLE:
                return new GoogleProvider($authToken, [
                    'baseUrl' => Yii::$app->params['googleAuth']['baseUrl'],
                ]);
            case self::PROVIDER_APPLE:
                return new AppleProvider($authToken, [
                    'baseUrl' => Yii::$app->params['appleAuth']['baseUrl'],
                    'audience' => Yii::$app->params['appleAuth']['audience'],
                ]);
            case self::PROVIDER_LINKEDIN:
                return new LinkedinProvider($authToken, [
                    'baseUrl' => Yii::$app->params['linkedinAuth']['baseUrl'],
                    'authUrl' => Yii::$app->params['linkedinAuth']['authUrl'],
                    'clientId' => Yii::$app->params['linkedinAuth']['clientId'],
                    'clientSecret' => Yii::$app->params['linkedinAuth']['clientSecret'],
                    'redirectUri' => Yii::$app->params['linkedinAuth']['redirectUri'] . $action,
                ]);
            default:
                throw new NotFoundHttpException(
                    'Invalid provider name' . ($providerName ? ": $providerName" : '')
                );
        }
    }
}