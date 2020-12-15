<?php declare(strict_types=1);

namespace Pollen\Embed\Contracts;

/**
 * @mixin \tiFy\Support\Concerns\BootableTrait
 * @mixin \Pollen\Embed\EmbedAwareTrait
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 */
interface EmbedProviderContract
{
    /**
     * Initialisation.
     *
     * @return static
     */
    public function boot(): EmbedProviderContract;

    /**
     * Récupération des informations associées à une Url du service.
     *
     * @param string $url
     *
     * @return EmbedFactoryContract
     */
    public function get(string $url): EmbedFactoryContract;

    /**
     * Récupération de l'alias de qualification.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Définition de l'alias de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): EmbedProviderContract;
}