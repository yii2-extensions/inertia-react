<?php

declare(strict_types=1);

namespace yii\inertia\react\tests;

use Yii;
use yii\helpers\Json;
use yii\inertia\Page;
use yii\inertia\react\tests\support\ApplicationFactory;
use yii\inertia\react\tests\support\stub\MockerFunctions;
use yii\web\Response;

/**
 * Base test case for yii2-framework/inertia-react tests.
 *
 * @author Wilmer Arambula <terabytesoftw@gmail.com>
 * @since 0.1
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        Yii::getLogger()->flush();
    }

    /**
     * Destroys the current application.
     */
    protected function destroyApplication(): void
    {
        ApplicationFactory::destroy();
    }

    /**
     * Extracts the page object from an HTML or JSON response.
     *
     * @param Response $response Response object to extract the page from.
     *
     * @return array Page data as an associative array.
     *
     * @phpstan-return array<string, mixed>
     */
    protected function extractPage(Response $response): array
    {
        if ($response->data instanceof Page) {
            return $response->data->jsonSerialize();
        }

        $matches = [];

        self::assertMatchesRegularExpression(
            '/<script type="application\/json">(.*?)<\/script>/s',
            (string) $response->content,
            'HTML response should contain an inline JSON script with the page payload.',
        );

        preg_match('/<script type="application\/json">(.*?)<\/script>/s', (string) $response->content, $matches);

        self::assertArrayHasKey(
            1,
            $matches,
            'Regex should match exactly one JSON script block.',
        );

        /** @phpstan-var array<string, mixed> */
        return Json::decode($matches[1]);
    }

    /**
     * Populates Yii::$app with a new web application configured for Inertia React tests.
     *
     * @param array $config Additional configuration for the application. This will be merged with the default
     * configuration used in tests.
     *
     * @phpstan-param array<string, mixed> $config
     */
    protected function mockWebApplication(array $config = []): void
    {
        ApplicationFactory::web($config);
    }

    /**
     * Sets the current absolute request URL for tests.
     *
     * @param string $url URL to set as the current request URL. This should be a path relative to the host, for
     * example, '/test'.
     */
    protected function setAbsoluteUrl(string $url): void
    {
        Yii::$app->getRequest()->setHostInfo('https://example.test');
        Yii::$app->getRequest()->setUrl($url);
    }

    protected function setUp(): void
    {
        parent::setUp();

        MockerFunctions::reset();

        $this->mockWebApplication();

        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->destroyApplication();
    }
}
