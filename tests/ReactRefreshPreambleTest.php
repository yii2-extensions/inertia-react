<?php

declare(strict_types=1);

namespace yii\inertia\react\tests;

use Yii;
use yii\inertia\Vite;

/**
 * Unit tests for {@see \yii\inertia\react\Bootstrap::reactRefreshPreambleProvider()}.
 *
 * Integration coverage for the React Refresh preamble closure wired by the React Bootstrap. The Bootstrap registers
 * `inertiaReact` as a {@see \yii\inertia\Vite} component whose `preambleProvider` closure loads the bundled
 * `src/preamble/react-refresh.js` asset and substitutes the resolved Vite dev server URL into the
 * `__VITE_DEV_SERVER_URL__` placeholder. These tests assert the end-to-end output of `renderTags()` in development mode
 * contains every line of the canonical Vite React preamble plus the `@vite/client` and entrypoint scripts.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class ReactRefreshPreambleTest extends TestCase
{
    public function testRenderTagsEmitsReactRefreshPreambleInDevModeByDefault(): void
    {
        /** @var Vite $vite */
        $vite = Yii::$app->get('inertiaReact');

        $vite->devMode = true;
        $vite->devServerUrl = 'http://localhost:5173';
        $vite->entrypoints = ['resources/js/app.jsx'];

        $tags = $vite->renderTags();

        self::assertStringContainsString(
            'import RefreshRuntime from "http://localhost:5173/@react-refresh"',
            $tags,
            'Development mode must import the React Refresh runtime from the dev server URL substituted into the asset.',
        );
        self::assertStringContainsString(
            'RefreshRuntime.injectIntoGlobalHook(window)',
            $tags,
            'Development mode must inject the React Refresh runtime into the global hook.',
        );
        self::assertStringContainsString(
            'window.$RefreshReg$ = () => {}',
            $tags,
            'Development mode must install the empty $RefreshReg$ stub.',
        );
        self::assertStringContainsString(
            'window.$RefreshSig$ = () => (type) => type',
            $tags,
            'Development mode must install the identity $RefreshSig$ stub.',
        );
        self::assertStringContainsString(
            'window.__vite_plugin_react_preamble_installed__ = true',
            $tags,
            'Development mode must set the Vite React preamble installed flag.',
        );
        self::assertStringContainsString(
            '<script type="module" src="http://localhost:5173/@vite/client"></script>',
            $tags,
            "Development mode must still emit the '@vite/client' tag after the preamble.",
        );
        self::assertStringContainsString(
            '<script type="module" src="http://localhost:5173/resources/js/app.jsx"></script>',
            $tags,
            'Development mode must still emit the entrypoint script after the preamble.',
        );
    }

    public function testRenderTagsOmitsPreambleWhenProviderIsCleared(): void
    {
        /** @var Vite $vite */
        $vite = Yii::$app->get('inertiaReact');

        $vite->devMode = true;
        $vite->devServerUrl = 'http://localhost:5173';
        $vite->entrypoints = ['resources/js/app.jsx'];
        $vite->preambleProvider = null;

        $tags = $vite->renderTags();

        self::assertStringNotContainsString(
            '@react-refresh',
            $tags,
            "Clearing 'preambleProvider' must omit the React Refresh runtime import.",
        );
        self::assertStringNotContainsString(
            '__vite_plugin_react_preamble_installed__',
            $tags,
            "Clearing 'preambleProvider' must omit the installed flag.",
        );
        self::assertStringContainsString(
            '<script type="module" src="http://localhost:5173/@vite/client"></script>',
            $tags,
            "Clearing 'preambleProvider' must still emit the '@vite/client' tag.",
        );
    }
}
