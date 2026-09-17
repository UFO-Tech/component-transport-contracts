<?php

namespace Ufo\Component\TransportContracts;

readonly class AsyncStampDTO
{
    public function __construct(
        public string $asyncDSN,
        public bool $highPriority = true,
        public array $extra = [],
    ) {}

}
