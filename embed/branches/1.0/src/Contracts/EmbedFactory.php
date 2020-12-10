<?php declare(strict_types=1);

namespace Pollen\Embed\Contracts;

use Embed\Extractor;

/**
 * @mixin \Embed\Embed
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 */
interface EmbedFactory
{
    /**
     * Délégation d'appel des attributs de l'api.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function __get(string $offset);

    /**
     * Délégation d'appel des méthodes de l'api.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments);

    /**
     * Récupération du pilote de délégation.
     *
     * @return object
     */
    public function delegateApiDriver(): object;

    /**
     * Récupération de l'url des données embarquées.
     *
     * @return string
     */
    public function getEmbedUrl(): string;

    /**
     * Récupération de la liste des informations.
     *
     * @return Extractor|object|array
     */
    public function getDatas();

    /**
     * Récupération de l'alias de qualification du fournisseur de service associé.
     *
     * @return string
     */
    public function getProviderAlias(): string;

    /**
     * Récupération de l'instance du fournisseur de service associé.
     *
     * @return EmbedProvider
     */
    public function provider(): EmbedProvider;

    /**
     * Rendu de l'affichage.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition des données associées
     *
     * @param Extractor|object|array
     *
     * @return static
     */
    public function setDatas($datas): EmbedFactory;
}