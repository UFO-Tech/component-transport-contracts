<?php

namespace Ufo\Component\TransportContracts;

readonly class AsyncTransportConfigDTO
{
    /**
     * @param string $name ім'я транспорту в Messenger
     * @param array<string, mixed> $options опції, що доповнюють опції за замовчуванням
     */
    public function __construct(
        public string $name,
        public string $dsn,
        public array $options = []
    ) {}

    public function applyTransportConfig(array $config): static
    {
        return new static($this->name, $this->dsn, array_replace_recursive($config, $this->options));
    }

}
