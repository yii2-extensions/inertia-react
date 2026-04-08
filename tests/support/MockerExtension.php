<?php

declare(strict_types=1);

namespace yii\inertia\react\tests\support;

use PHPUnit\Event\Test\{PreparationStarted, PreparationStartedSubscriber};
use PHPUnit\Event\TestSuite\{Started, StartedSubscriber};
use PHPUnit\Runner\Extension\{Extension, Facade, ParameterCollection};
use PHPUnit\TextUI\Configuration\Configuration;
use Xepozz\InternalMocker\{Mocker, MockerState};
use yii\inertia\react\tests\support\stub\MockerFunctions;

/**
 * PHPUnit extension that registers internal-function mocks for test execution.
 *
 * Subscribes to PHPUnit's test lifecycle via {@see \PHPUnit\Runner\Extension\Facade} so a curated set of built-in
 * PHP functions (for example, `file_get_contents`) are redirected through {@see \Xepozz\InternalMocker\Mocker} and can
 * be deterministically controlled from {@see stub\MockerFunctions} during each test run.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class MockerExtension implements Extension
{
    /**
     * Registers the internal-function mocks on the PHPUnit runner.
     *
     * Subscribes to two lifecycle events.
     *
     * - Loads the mock definitions and saves the initial {@see MockerState} snapshot.
     * - Restores the mocker state before every test so mutations from the previous test never leak across cases.
     *
     * @param Configuration $configuration PHPUnit configuration (unused; kept for interface compliance).
     * @param Facade $facade Subscriber facade used to register lifecycle listeners.
     * @param ParameterCollection $parameters Extension-specific parameters (unused).
     */
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscribers(
            new class implements StartedSubscriber {
                public function notify(Started $event): void
                {
                    MockerExtension::load();
                }
            },
            new class implements PreparationStartedSubscriber {
                public function notify(PreparationStarted $event): void
                {
                    MockerState::resetState();
                }
            },
        );
    }

    /**
     * Loads the internal-function mocks into {@see \Xepozz\InternalMocker\Mocker} and saves the resulting state.
     *
     * Idempotent: safe to call multiple times across suite starts. The function stubs forward their arguments to the
     * corresponding {@see MockerFunctions} hooks so individual tests can drive deterministic return values or
     * exceptions through static toggles.
     */
    public static function load(): void
    {
        $fileGetContents = static fn(
            string $filename,
            mixed ...$args,
        ): string|false => MockerFunctions::file_get_contents($filename, ...$args);

        $mocks = [
            [
                'namespace' => 'yii\inertia',
                'name' => 'file_get_contents',
                'function' => $fileGetContents,
            ],
            [
                'namespace' => 'yii\inertia\react',
                'name' => 'file_get_contents',
                'function' => $fileGetContents,
            ],
        ];

        $mocker = new Mocker(stubPath: __DIR__ . '/stub/stubs.php');

        $mocker->load($mocks);

        MockerState::saveState();
    }
}
