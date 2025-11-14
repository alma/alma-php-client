<?php

namespace Alma\API\Tests\Unit\Domain\Entity;

use Alma\API\Domain\ValueObject\Environment;
use Alma\API\Domain\ValueObject\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    public function environmentModesProvider(): array
    {
        return [
            ['live', Environment::LIVE_API_URL],
            ['test', Environment::SANDBOX_API_URL],
            ['custom', 'https://custom.api.url'],
        ];
    }

    /** @dataProvider environmentModesProvider */
    public function testEnvironmentModesAreSetCorrectly($mode, $expectedUrl)
    {
        $customUrl = $mode === 'custom' ? 'https://custom.api.url' : '';
        $environment = new Environment($mode, $customUrl);

        $this->assertEquals($mode, $environment->getMode());
        $this->assertEquals(Uri::fromString($expectedUrl), $environment->getBaseUri());
    }

    public function testInvalidModeDefaultsToLive()
    {
        $environment = new Environment('invalid_mode');
        $this->assertEquals(Environment::LIVE_MODE, $environment->getMode());
        $this->assertEquals(Uri::fromString(Environment::LIVE_API_URL), $environment->getBaseUri());
    }

    public function testCustomModeWithoutUrlThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Custom API URL must be provided for custom mode.');
        new Environment(Environment::CUSTOM_MODE);
    }

    public function testEnvironmentsWithSameModeAreEqual()
    {
        $environment1 = new Environment(Environment::LIVE_MODE);
        $environment2 = new Environment(Environment::LIVE_MODE);

        $this->assertTrue($environment1->equals($environment2));
    }

    public function testEnvironmentsWithDifferentModesAreNotEqual()
    {
        $environment1 = new Environment(Environment::LIVE_MODE);
        $environment2 = new Environment(Environment::TEST_MODE);

        $this->assertFalse($environment1->equals($environment2));
    }

    public function testIsLiveModeReturnsTrueForLiveMode()
    {
        $environment = new Environment(Environment::LIVE_MODE);
        $this->assertTrue($environment->isLiveMode());
    }

    public function testIsTestModeReturnsTrueForTestMode()
    {
        $environment = new Environment(Environment::TEST_MODE);
        $this->assertTrue($environment->isTestMode());
    }

    public function testIsCustomModeReturnsTrueForCustomMode()
    {
        $environment = new Environment(Environment::CUSTOM_MODE, 'https://custom.api.url');
        $this->assertTrue($environment->isCustomMode());
    }

    public function testToStringReturnsMode()
    {
        $environment = new Environment(Environment::TEST_MODE);
        $this->assertEquals(Environment::TEST_MODE, (string)$environment);
    }
}
