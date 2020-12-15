<?php declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\Contracts\EmbedFactoryContract;

interface EmbedVideoFactoryInterface extends EmbedFactoryContract
{
    /**
     * Récupération de la liste des sources vidéo.
     *
     * @return string[]|array
     */
    public function getSources(): array;

    /**
     * Définition d'une source de vidéo complémentaire.
     *
     * @param string $src
     *
     * @return static
     */
    public function setSource(string $src): EmbedVideoFactoryInterface;
}