<?php declare(strict_types=1);

namespace Pollen\Embed\Contracts;

interface EmbedYoutubeFactory extends EmbedFactory
{
    /**
     * Récupération de l'identifiant de qualification de la vidéo.
     *
     * @return string|null
     */
    public function getVideoId(): ?string;
}