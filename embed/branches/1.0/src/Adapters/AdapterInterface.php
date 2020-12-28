<?php

declare(strict_types=1);

namespace Pollen\Embed\Adapters;

/**
 * @mixin \Pollen\Embed\EmbedAwareTrait
 */
interface AdapterInterface
{
    /**
     * Récupération des champs par défaut.
     *
     * @return array
     */
    public function getDefaultFields(): array;

    /**
     * Récupération des portions d'affichage par défaut.
     *
     * @return array
     */
    public function getDefaultPartials(): array;

    /**
     * Récupération des fournisseurs de services par défaut.
     *
     * @return array
     */
    public function getDefaultProviders(): array;
}
