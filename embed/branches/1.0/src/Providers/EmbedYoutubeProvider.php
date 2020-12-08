<?php declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\Contracts\EmbedYoutubeProvider as EmbedYoutubeProviderContract;
use Pollen\Embed\Contracts\EmbedFactory as EmbedFactoryContract;
use Pollen\Embed\EmbedBaseProvider;

class EmbedYoutubeProvider extends EmbedBaseProvider implements EmbedYoutubeProviderContract
{
    /**
     * @inheritDoc
     */
    public function get(string $url): EmbedFactoryContract
    {
        return new EmbedYoutubeFactory($url, $this);
    }

    /**
     * @inheritDoc
     */
    public function fetchVideoIdFromUrl(string $url): ?string
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);

        return $match[1] ?? null;
    }
}