<?php

declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\EmbedFactoryInterface;
use Pollen\Embed\EmbedProviderInterface;

interface EmbedVideoProviderInterface extends EmbedProviderInterface
{
    /**
     * {@inheritDoc}
     *
     * @return EmbedVideoFactoryInterface
     */
    public function get(string $url): EmbedFactoryInterface;

}
