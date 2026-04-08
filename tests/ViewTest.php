<?php

declare(strict_types=1);

namespace yii\inertia\react\tests;

use Yii;
use yii\base\{Component, InvalidConfigException};
use yii\inertia\{Inertia, Vite};
use yii\inertia\react\Bootstrap;
use yii\web\{Application, Response};

/**
 * Unit tests for {@see \yii\inertia\react\Bootstrap}.
 *
 * Integration coverage for the React-aware root view registered by the React Bootstrap and rendered through the base
 * Inertia Manager.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class ViewTest extends TestCase
{
    public function testReactRootViewRendersViteTagsAndPagePayload(): void
    {
        $this->setAbsoluteUrl('/dashboard');

        $response = Inertia::render(
            'Dashboard',
            [
                'stats' => [
                    'visits' => 42,
                ],
            ],
        );

        $content = (string) $response->content;

        $page = $this->extractPage($response);

        self::assertSame(
            Response::FORMAT_HTML,
            $response->format,
            'View response should be HTML format.',
        );
        self::assertStringContainsString(
            '<meta name="csrf-param"',
            $content,
            'View should include CSRF meta tag.',
        );
        self::assertStringContainsString(
            '<link href="/build/assets/app-C9kL1mN2.css" rel="stylesheet">',
            $content,
            'View should include the CSS stylesheet from the manifest.',
        );
        self::assertStringContainsString(
            '<script type="module" src="/build/assets/app-A1b2C3d4.js"></script>',
            $content,
            'View should include the module script from the manifest.',
        );
        self::assertStringContainsString(
            '<script type="application/json">',
            $content,
            'View should include the Inertia page payload script.',
        );
        self::assertSame(
            'Dashboard',
            $page['component'], // @phpstan-ignore offsetAccess.notFound
            'Page payload should contain the rendered component name.',
        );
        self::assertSame(
            ['visits' => 42],
            $page['props']['stats'], // @phpstan-ignore offsetAccess.notFound, offsetAccess.nonOffsetAccessible
            'Page payload should contain the passed props.',
        );
    }
    public function testThrowInvalidConfigExceptionWhenNonViteComponent(): void
    {
        $this->destroyApplication();

        new Application(
            [
                'id' => 'testapp',
                'basePath' => dirname(__DIR__) . '/tests',
                'bootstrap' => [
                    Bootstrap::class,
                ],
                'aliases' => [
                    '@tests' => dirname(__DIR__) . '/tests',
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

        Yii::$app->set('inertiaReact', new Component());

        $this->setAbsoluteUrl('/dashboard');
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(
            "The 'inertiaReact' application component must be an instance of " . Vite::class . '.',
        );

        Inertia::render('Dashboard', ['stats' => ['visits' => 42]]);
    }
}
