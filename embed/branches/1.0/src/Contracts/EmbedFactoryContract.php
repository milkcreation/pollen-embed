<?php declare(strict_types=1);

namespace Pollen\Embed\Contracts;

use Embed\Extractor;

/**
 * @mixin \Embed\Extractor
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 */
interface EmbedFactoryContract
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
     * @return Extractor|object
     */
    public function delegateApiDriver(): object;

    /**
     * Récupération de l'url des données embarquées.
     *
     * @return string
     */
    public function getEmbedUrl(): string;

    /**
     * Récupération de l'url d'accès aux données embarquées.
     *
     * @return string|null
     */
    public function getOEmbedEndpoint(): ?string;

    /**
     * Récupération de l'alias de qualification du fournisseur de service associé.
     *
     * @return string
     */
    public function getProviderAlias(): string;

    /**
     * Récupération de l'url.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Vérifie si le contenu embarqué est associé à un fournisseur de services en ligne.
     *
     * @return bool
     */
    public function isEmbeded(): bool;

    /**
     * Récupération de l'instance du fournisseur de service associé.
     *
     * @return EmbedProviderContract
     */
    public function provider(): EmbedProviderContract;

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
    public function setDatas($datas): EmbedFactoryContract;
}
