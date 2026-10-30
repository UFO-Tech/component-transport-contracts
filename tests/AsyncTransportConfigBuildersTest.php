<?php

namespace Ufo\Component\TransportContracts\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ufo\Component\TransportContracts\AsyncTransportConfigBuilderInterface;
use Ufo\Component\TransportContracts\AsyncTransportConfigBuilders;
use Ufo\Component\TransportContracts\AsyncTransportConfigDTO;

class AsyncTransportConfigBuildersTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir().'/async-builders-'.uniqid();
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        @unlink($this->dir.'/composer.json');
        @rmdir($this->dir);
    }

    public function testBuilderNamedInExtraIsCreated(): void
    {
        $builders = AsyncTransportConfigBuilders::fromComposerJson($this->composerJson(['extra' => [
            AsyncTransportConfigBuilders::EXTRA_KEY => FakeConfigBuilder::class,
        ]]));

        self::assertCount(1, $builders);
        self::assertInstanceOf(FakeConfigBuilder::class, $builders[0]);
    }

    public function testListOfBuildersIsAccepted(): void
    {
        $builders = AsyncTransportConfigBuilders::fromComposerJson($this->composerJson(['extra' => [
            AsyncTransportConfigBuilders::EXTRA_KEY => [FakeConfigBuilder::class, FakeConfigBuilder::class],
        ]]));

        self::assertCount(2, $builders);
    }

    public function testPackageWithoutExtraGivesNothing(): void
    {
        self::assertSame([], AsyncTransportConfigBuilders::fromComposerJson($this->composerJson(['name' => 'acme/x'])));
        self::assertSame([], AsyncTransportConfigBuilders::fromComposerJson($this->dir.'/missing.json'));
    }

    public function testClassThatIsNotABuilderIsRefused(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(\stdClass::class);

        AsyncTransportConfigBuilders::fromComposerJson($this->composerJson(['extra' => [
            AsyncTransportConfigBuilders::EXTRA_KEY => \stdClass::class,
        ]]));
    }

    public function testDiscoverFindsNothingWithoutAdapters(): void
    {
        self::assertSame([], AsyncTransportConfigBuilders::discover());
    }

    public function testDiscoverScansOnlyOnce(): void
    {
        $discovered = new \ReflectionProperty(AsyncTransportConfigBuilders::class, 'discovered');
        $discovered->setValue(null, ['fake' => $builder = new FakeConfigBuilder()]);

        try {
            self::assertSame(['fake' => $builder], AsyncTransportConfigBuilders::discover());
        } finally {
            $discovered->setValue(null, null);
        }
    }

    private function composerJson(array $data): string
    {
        file_put_contents($this->dir.'/composer.json', json_encode($data));

        return $this->dir.'/composer.json';
    }
}

class FakeConfigBuilder implements AsyncTransportConfigBuilderInterface
{
    public function getType(): string
    {
        return 'fake';
    }

    public function getQueueName(string $transportName): string
    {
        return $transportName;
    }

    public function build(AsyncTransportConfigDTO $config): AsyncTransportConfigDTO
    {
        return $config;
    }
}
