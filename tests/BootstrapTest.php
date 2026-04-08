<?php

declare(strict_types=1);

namespace yii\inertia\react\tests;

use Yii;
use yii\inertia\{Manager, Vite};
use yii\inertia\react\Bootstrap;
use yii\web\Application;

/**
 * Unit tests for {@see \yii\inertia\react\Bootstrap}.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class BootstrapTest extends TestCase
{
    public function testBootstrapPreservesCustomInertiaRootView(): void
    {
        $this->destroyApplication();

        $this->mockWebApplication(
            [
                'components' => [
                    'inertia' => [
                        'class' => Manager::class,
                        'rootView' => '@app/views/layouts/custom.php',
                    ],
                ],
            ],
        );

        $manager = Yii::$app->get('inertia');

        self::assertInstanceOf(
            Manager::class,
            $manager,
            'Bootstrap should register the Manager component.',
        );
        self::assertSame(
            '@app/views/layouts/custom.php',
            $manager->rootView,
            'Bootstrap should preserve a custom root view when already configured.',
        );
    }

    public function testBootstrapRegistersAliasAndReactComponent(): void
    {
        self::assertSame(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src',
            Yii::getAlias('@inertia-react'),
            'Bootstrap should register the @inertia-react alias pointing to the src/ directory.',
        );
        self::assertInstanceOf(
            Vite::class,
            Yii::$app->get('inertiaReact'),
            'Bootstrap should register the inertiaReact component as a Vite instance.',
        );
    }

    public function testBootstrapRegistersDefaultReactComponentWhenNotConfigured(): void
    {
        $this->destroyApplication();

        new Application(
            [
                'id' => 'testapp',
                'aliases' => [
                    '@tests' => dirname(__DIR__) . '/tests',
                ],
                'basePath' => dirname(__DIR__) . '/tests',
                'bootstrap' => [
                    Bootstrap::class,
                ],
                'components' => [
                    'request' => [
                        'cookieValidationKey' => 'test',
                        'hostInfo' => 'https://example.test',
                        'scriptFile' => dirname(__DIR__) . '/index.php',
                        'scriptUrl' => '/index.php',
                        'isConsoleRequest' => false,
                    ],
                ],
                'vendorPath' => dirname(__DIR__) . '/vendor',
            ],
        );

        self::assertInstanceOf(
            Vite::class,
            Yii::$app->get('inertiaReact'),
            'Bootstrap should register a default Vite component when inertiaReact is not configured.',
        );
    }

    public function testBootstrapSwitchesDefaultRootViewToReactView(): void
    {
        $manager = Yii::$app->get('inertia');

        self::assertInstanceOf(
            Manager::class,
            $manager,
            'Bootstrap should register the Inertia Manager component.',
        );
        self::assertSame(
            '@inertia-react/views/app.php',
            $manager->rootView,
            'Bootstrap should switch the default root view to the React-aware template.',
        );
    }
}
