<?php

declare(strict_types=1);

namespace yii\inertia\react;

use Closure;
use Yii;
use yii\base\{BootstrapInterface, InvalidConfigException};
use yii\inertia\{Manager, Vite};

use function sprintf;
use function str_replace;

/**
 * Bootstraps the React adapter for yii2-framework/inertia.
 *
 * Delegates the base Inertia bootstrap, registers the `@inertia-react` alias, registers the canonical
 * {@see \yii\inertia\Vite} renderer under the `inertiaReact` component id with a {@see $preambleProvider} closure that
 * loads the React Refresh preamble asset shipped by this package, and switches the default Inertia root view to the
 * React-aware view shipped by this package.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class Bootstrap implements BootstrapInterface
{
    /**
     * Placeholder substituted at preamble load time with the resolved Vite dev server URL.
     */
    private const DEV_SERVER_URL_PLACEHOLDER = '__VITE_DEV_SERVER_URL__';
    /**
     * Filesystem path to the React Refresh preamble JavaScript asset shipped by the package.
     */
    private const REACT_REFRESH_PREAMBLE_PATH = __DIR__ . '/preamble/react-refresh.js';

    public function bootstrap($app): void
    {
        (new \yii\inertia\Bootstrap())->bootstrap($app);

        Yii::setAlias('@inertia-react', __DIR__);

        if (!$app->has('inertiaReact')) {
            $app->set('inertiaReact', [
                'class' => Vite::class,
                'preambleProvider' => self::reactRefreshPreambleProvider(),
            ]);
        }

        $manager = $app->get('inertia');

        if ($manager instanceof Manager && $manager->rootView === '@inertia/views/app.php') {
            $manager->rootView = '@inertia-react/views/app.php';
        }
    }

    /**
     * Loads the React Refresh preamble template once and returns a closure that substitutes the dev server URL.
     *
     * Exposed publicly so application config and tests can opt into the same canonical preamble without re-reading the
     * asset themselves.
     *
     * Usage example:
     *
     * ```php
     * 'inertiaReact' => [
     *     'class' => \yii\inertia\Vite::class,
     *     'preambleProvider' => \yii\inertia\react\Bootstrap::reactRefreshPreambleProvider(),
     * ],
     * ```
     *
     * @throws InvalidConfigException if the bundled preamble asset cannot be read from disk.
     *
     * @return Closure Closure that, given a resolved dev server URL, returns the inline preamble script body ready
     * to be wrapped in a `<script type="module">` tag by {@see Vite::renderTags()}.
     *
     * @phpstan-return Closure(string): string
     */
    public static function reactRefreshPreambleProvider(): Closure
    {
        $template = file_get_contents(self::REACT_REFRESH_PREAMBLE_PATH);

        if ($template === false) {
            throw new InvalidConfigException(
                sprintf(
                    'Unable to read the bundled React Refresh preamble asset "%s".',
                    self::REACT_REFRESH_PREAMBLE_PATH,
                ),
            );
        }

        return static fn(string $devServerUrl): string => str_replace(
            self::DEV_SERVER_URL_PLACEHOLDER,
            $devServerUrl,
            $template,
        );
    }
}
