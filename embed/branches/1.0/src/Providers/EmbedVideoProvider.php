<?php declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\Contracts\EmbedVideoProvider as EmbedVideoProviderContract;
use Pollen\Embed\Contracts\EmbedFactory as EmbedFactoryContract;
use Pollen\Embed\EmbedBaseProvider;

class EmbedVideoProvider extends EmbedBaseProvider implements EmbedVideoProviderContract
{
    /**
     * @inheritDoc
     */
    public function get(string $url): EmbedFactoryContract
    {
        return new EmbedVideoFactory($url, $this);
    }
}