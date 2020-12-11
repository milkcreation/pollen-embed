<?php declare(strict_types=1);

namespace Pollen\Embed\Contracts;

interface EmbedYoutubeProvider extends EmbedProvider
{
    /**
     * {@inheritDoc}
     *
     * @return EmbedYoutubeFactory
     */
    public function get(string $url): EmbedFactory;

    /**
     * Retrouve l'identifiant de qualification d'une vidéo depuis son url.
     *
     * @param string $url
     *
     * @return string
     */
    public function fetchVideoIdFromUrl(string $url): ?string;
}