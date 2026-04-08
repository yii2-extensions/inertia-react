<?php

declare(strict_types=1);

namespace yii\inertia\react\tests\support\stub;

/**
 * Stateful stub for internal PHP functions hijacked during test execution.
 *
 * Provides minimal failure injection for `file_get_contents`.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
final class MockerFunctions
{
    /**
     * Whether the next {@see file_get_contents()} invocation must return `false` to simulate an unreadable file.
     */
    private static bool $fileGetContentsShouldFail = false;

    /**
     * Either short-circuits with `false` or delegates to PHP's native `file_get_contents`.
     *
     * @param string $filename Filesystem path forwarded to the native function.
     * @param mixed ...$args Optional positional arguments forwarded verbatim to the native function.
     *
     * @return false|string File contents on success, or `false` when failure is simulated or the underlying call
     * fails.
     */
    public static function file_get_contents(string $filename, mixed ...$args): false|string
    {
        if (self::$fileGetContentsShouldFail) {
            return false;
        }

        return \file_get_contents($filename, ...$args); // @phpstan-ignore argument.type
    }

    /**
     * Resets every recorded flag to its pristine state.
     *
     * Tests must call this in their `setUp()` to guarantee isolation across test cases.
     */
    public static function reset(): void
    {
        self::$fileGetContentsShouldFail = false;
    }

    /**
     * Toggles whether the next {@see file_get_contents()} invocation must return `false` to simulate an unreadable
     * file.
     *
     * @param bool $shouldFail `true` to simulate failure, or `false` to restore normal behavior.
     */
    public static function setFileGetContentsShouldFail(bool $shouldFail = true): void
    {
        self::$fileGetContentsShouldFail = $shouldFail;
    }
}
