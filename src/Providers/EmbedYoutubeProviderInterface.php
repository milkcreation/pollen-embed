<?php declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\Contracts\EmbedFactoryContract;
use Pollen\Embed\Contracts\EmbedProviderContract;

interface EmbedYoutubeProviderInterface extends EmbedProviderContract
{
    /**
     * {@inheritDoc}
     *
     * @return EmbedYoutubeFactoryInterface
     */
    public function get(string $url): EmbedFactoryContract;

    /**
     * Retrouve l'identifiant de qualification d'une vidéo depuis son url.
     *
     * @param string $url
     *
     * @return string
     */
    public function fetchVideoIdFromUrl(string $url): ?string;
}
