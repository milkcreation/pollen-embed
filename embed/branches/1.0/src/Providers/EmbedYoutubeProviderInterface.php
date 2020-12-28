<?php

declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\EmbedFactoryInterface;
use Pollen\Embed\EmbedProviderInterface;

interface EmbedYoutubeProviderInterface extends EmbedProviderInterface
{
    /**
     * {@inheritDoc}
     *
     * @return EmbedYoutubeFactoryInterface
     */
    public function get(string $url): EmbedFactoryInterface;

    /**
     * Retrouve l'identifiant de qualification d'une vidéo depuis son url.
     *
     * @param string $url
     *
     * @return string
     */
    public function fetchVideoIdFromUrl(string $url): ?string;
}
