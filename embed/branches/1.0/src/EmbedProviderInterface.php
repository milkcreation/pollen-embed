<?php declare(strict_types=1);

namespace Pollen\Embed;

/**
 * @mixin \tiFy\Support\Concerns\BootableTrait
 * @mixin \Pollen\Embed\EmbedAwareTrait
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 */
interface EmbedProviderInterface
{
    /**
     * Initialisation.
     *
     * @return static
     */
    public function boot(): EmbedProviderInterface;

    /**
     * Récupération des informations associées à une Url du service.
     *
     * @param string $url
     *
     * @return EmbedFactoryInterface
     */
    public function get(string $url): EmbedFactoryInterface;

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
    public function setAlias(string $alias): EmbedProviderInterface;
}
