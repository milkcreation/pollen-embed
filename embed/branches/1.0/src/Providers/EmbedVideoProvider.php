<?php declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\EmbedFactoryInterface;
use Pollen\Embed\EmbedBaseProvider;

class EmbedVideoProvider extends EmbedBaseProvider implements EmbedVideoProviderInterface
{
    /**
     * @inheritDoc
     */
    public function get(string $url): EmbedFactoryInterface
    {
        return new EmbedVideoFactory($url, $this);
    }
}
