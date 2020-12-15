<?php declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\Contracts\EmbedFactoryContract;
use Pollen\Embed\EmbedBaseProvider;

class EmbedVideoProvider extends EmbedBaseProvider implements EmbedVideoProviderInterface
{
    /**
     * @inheritDoc
     */
    public function get(string $url): EmbedFactoryContract
    {
        return new EmbedVideoFactory($url, $this);
    }
}