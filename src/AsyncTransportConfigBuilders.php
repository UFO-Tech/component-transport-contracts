<?php

namespace Ufo\Component\TransportContracts;

use Composer\InstalledVersions;
use InvalidArgumentException;

use function class_exists;
use function file_get_contents;
use function is_a;
use function is_file;
use function json_decode;
use function sprintf;

/**
 * Білдери встановлених адаптерів: пакет типу ufo-async-transport
 * називає клас білдера в extra.ufo-async-transport-builder свого composer.json.
 */
final class AsyncTransportConfigBuilders
{
    public const string PACKAGE_TYPE = 'ufo-async-transport';
    public const string EXTRA_KEY = 'ufo-async-transport-builder';

    /**
     * @var array<string, AsyncTransportConfigBuilderInterface>|null
     */
    private static ?array $discovered = null;

    /**
     * @return array<string, AsyncTransportConfigBuilderInterface> ключ — тип транспорту
     * @throws InvalidArgumentException
     */
    public static function discover(): array
    {
        return self::$discovered ??= self::scan();
    }

    /**
     * @return array<string, AsyncTransportConfigBuilderInterface>
     * @throws InvalidArgumentException
     */
    private static function scan(): array
    {
        $builders = [];
        foreach (InstalledVersions::getInstalledPackagesByType(self::PACKAGE_TYPE) as $package) {
            $path = InstalledVersions::getInstallPath($package);
            if ($path === null) continue;

            foreach (self::fromComposerJson($path.'/composer.json') as $builder) {
                $builders[$builder->getType()] = $builder;
            }
        }

        return $builders;
    }

    /**
     * @return list<AsyncTransportConfigBuilderInterface>
     * @throws InvalidArgumentException
     */
    public static function fromComposerJson(string $composerJson): array
    {
        if (!is_file($composerJson)) return [];

        $classes = (array)(json_decode((string)file_get_contents($composerJson), true)['extra'][self::EXTRA_KEY] ?? []);
        $builders = [];
        foreach ($classes as $class) {
            if (!class_exists($class) || !is_a($class, AsyncTransportConfigBuilderInterface::class, true)) {
                throw new InvalidArgumentException(sprintf('%s names "%s" in extra.%s, which is not an existing %s', $composerJson, $class, self::EXTRA_KEY, AsyncTransportConfigBuilderInterface::class));
            }
            $builders[] = new $class();
        }

        return $builders;
    }
}
