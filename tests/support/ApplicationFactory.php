<?php

declare(strict_types=1);

namespace yii\inertia\react\tests\support;

use Yii;
use yii\helpers\ArrayHelper;
use yii\inertia\react\Bootstrap;
use yii\inertia\Vite;
use yii\web\Application;

/**
 * Creates Yii application instances for tests.
 *
 * Provides a deterministic web application factory used by the PHPUnit suite to bootstrap a fresh
 * {@see \yii\web\Application} per test with the React adapter wiring pre-configured.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class ApplicationFactory
{
    /**
     * A random string used as the cookie validation key for test applications.
     */
    private const COOKIE_VALIDATION_KEY = 'test-cookie-validation-key';

    /**
     * Destroys the current application.
     *
     * Closes any open session bound to the current application and nulls out the {@see \Yii::$app} singleton so the
     * next test starts from a clean state.
     */
    public static function destroy(): void
    {
        if (Yii::$app !== null && Yii::$app->has('session', true)) {
            Yii::$app->session->close();
        }

        Yii::$app = null; // @phpstan-ignore assign.propertyType (Yii2 test teardown pattern)
    }

    /**
     * Creates a web application with the React bootstrap configured.
     *
     * @param array $override Configuration values to override the defaults from {@see commonBase()} with.
     *
     * @phpstan-param array<string, mixed> $override
     */
    public static function web(array $override = []): void
    {
        new Application(ArrayHelper::merge(self::commonBase(), $override));
    }

    /**
     * Returns the base configuration for a web application with the React bootstrap configured.
     *
     * @return array Base configuration array ready to be merged with per-test overrides.
     *
     * @phpstan-return array<string, mixed>
     */
    private static function commonBase(): array
    {
        return [
            'id' => 'testapp',
            'basePath' => dirname(__DIR__),
            'vendorPath' => dirname(__DIR__, 2) . '/vendor',
            'bootstrap' => [
                Bootstrap::class,
            ],
            'aliases' => [
                '@root' => dirname(__DIR__, 2),
                '@tests' => dirname(__DIR__),
            ],
            'components' => [
                'inertiaReact' => [
                    'class' => Vite::class,
                    'baseUrl' => '@web/build',
                    'entrypoints' => [
                        'resources/js/app.jsx',
                    ],
                    'manifestPath' => '@tests/data/build/.vite/manifest.json',
                    'preambleProvider' => Bootstrap::reactRefreshPreambleProvider(),
                ],
                'request' => [
                    'cookieValidationKey' => self::COOKIE_VALIDATION_KEY,
                    'hostInfo' => 'https://example.test',
                    'scriptFile' => dirname(__DIR__, 2) . '/index.php',
                    'scriptUrl' => '/index.php',
                    'isConsoleRequest' => false,
                ],
            ],
        ];
    }
}
