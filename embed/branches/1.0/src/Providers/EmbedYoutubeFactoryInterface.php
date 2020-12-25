<?php declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\Contracts\EmbedFactoryContract;

interface EmbedYoutubeFactoryInterface extends EmbedFactoryContract
{
    /**
     * Récupération de l'identifiant de qualification de la vidéo.
     *
     * @return string|null
     */
    public function getVideoId(): ?string;
}
