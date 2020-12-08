<?php declare(strict_types=1);

namespace Pollen\Embed\Contracts;

/**
 * @mixin \tiFy\Support\Concerns\BuildableTrait
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 */
interface EmbedProvider
{
    /**
     * Initialisation.
     *
     * @return static
     */
    public function build(): EmbedProvider;

    /**
     * Récupération des informations associées à une Url du service.
     *
     * @param string $url
     *
     * @return EmbedFactory
     */
    public function get(string $url): EmbedFactory;

    /**
     * Récupération de l'alias de qualification.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de l'instance du gestionnaire de services.
     *
     * @return Embed
     */
    public function embedManager(): Embed;

    /**
     * Définition de l'alias de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): EmbedProvider;

    /**
     * Définition du gestionnaire de services.
     *
     * @param Embed $embedManager
     *
     * @return static
     */
    public function setEmbedManager(Embed $embedManager): EmbedProvider;
}