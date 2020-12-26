<?php declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\Contracts\EmbedFactoryContract;
use Pollen\Embed\Contracts\EmbedProviderContract;

interface EmbedVideoProviderInterface extends EmbedProviderContract
{
    /**
     * {@inheritDoc}
     *
     * @return EmbedVideoFactoryInterface
     */
    public function get(string $url): EmbedFactoryContract;

}
