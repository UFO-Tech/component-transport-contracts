<?php

namespace Ufo\Component\TransportContracts;

/**
 * Збирає конфіг транспорту для framework.messenger.transports.
 * Працює до розв'язання env: DSN не розбирає, ім'я черги кладе в options.
 */
interface AsyncTransportConfigBuilderInterface
{
    public function getType(): string;

    /** Ім'я черги чи топіка для транспорту з цим іменем. */
    public function getQueueName(string $transportName): string;

    public function build(AsyncTransportConfigDTO $config): AsyncTransportConfigDTO;
}
