<?php

namespace Alma\Client\Tests\Unit\Domain\Entity;

use Alma\Client\Domain\ValueObject\Environment;
use Alma\Client\Domain\ValueObject\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    public static function environmentModesProvider(): array
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

    public static function invalidModesProvider(): array
    {
        return [
            ['invalid_mode'],
            ['Live'],
            ['Test'],
            ['LIVE'],
        ];
    }

    /** @dataProvider invalidModesProvider */
    public function testInvalidModeThrowsException(string $invalidMode)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid environment mode "%s". Allowed values are: live, test, custom.',
            $invalidMode
        ));
        new Environment($invalidMode);
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
