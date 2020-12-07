<?php declare(strict_types=1);

namespace Pollen\Embed\Contracts;

interface EmbedDriver
{
    /**
     * Récupération de l'instance du gestionnaire.
     *
     * @return Embed
     */
    public function embedManager(): Embed;
}