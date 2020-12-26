<?php declare(strict_types=1);

namespace Pollen\Embed\Contracts;

use Exception;
use Pollen\Embed\Adapters\AdapterInterface;
use Pollen\Embed\EmbedFactoryInterface;
use Pollen\Embed\EmbedProviderInterface;
use Pollen\Embed\Providers\EmbedVideoFactoryInterface;
use Pollen\Embed\Providers\EmbedYoutubeFactoryInterface;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Support\ParamsBag;

/**
 * @mixin \tiFy\Support\Concerns\BootableTrait
 * @mixin \tiFy\Support\Concerns\ContainerAwareTrait
 */
interface EmbedContract
{
    /**
     * Récupération de l'instance courante.
     *
     * @return static
     *
     * @throws Exception
     */
    public static function instance(): EmbedContract;

    /**
     * Chargement.
     *
     * @return static
     *
     * @throws Exception
     */
    public function boot(): EmbedContract;

    /**
     * Récupération de paramètre|Définition de paramètres|Instance du gestionnaire de paramètre.
     *
     * @param string|array|null $key Clé d'indice du paramètre à récupérer|Liste des paramètre à définir.
     * @param mixed $default Valeur de retour par défaut lorsque la clé d'indice est une chaine de caractère.
     *
     * @return ParamsBag|int|string|array|object
     */
    public function config($key = null, $default = null);

    /**
     * Retourne l'instance des données d'un service associé à une url.
     *
     * @param string $url
     *
     * @return EmbedFactoryInterface
     *
     * @throws Exception
     */
    public function dispatchFactory(string $url): EmbedFactoryInterface;

    /**
     * Récupération de l'instance de l'adapteur.
     *
     * @return AdapterInterface|null
     */
    public function getAdapter(): ?AdapterInterface;

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

    /**
     * Récupération de l'url d'accès aux données embarquées d'un contenu distribué par un fournisseur de service.
     *
     * @param string $url Url du contenu distribué
     * @param array $params Liste des paramètres d'url complémentaires
     *
     * @return string
     */
    public function getOEmbedEndpoint(string $url, array $params = []): ?string;

    /**
     * Récupération d'un fournisseur de service selon son nom de qualification.
     *
     * @param string $alias
     *
     * @return EmbedProviderInterface|null
     */
    public function getProvider(string $alias): ?EmbedProviderInterface;

    /**
     * Déclaration d'un fournisseur de service.
     *
     * @param string $alias
     * @param EmbedProviderInterface|string|array $providerDefinition
     *
     * @return EmbedContract
     */
    public function registerProvider(string $alias, $providerDefinition = []): EmbedContract;

    /**
     * Chemin absolu vers une ressources (fichier|répertoire).
     *    public function __construct() {
        exit;
    }
     * @param string|null $path Chemin relatif vers la ressource.
     *
     * @return LocalFilesystem|string|null
     */
    public function resources(?string $path = null);

    /**
     * Définition de l'adapteur associé.
     *
     * @param AdapterInterface $adapter
     *
     * @return static
     */
    public function setAdapter(AdapterInterface $adapter): EmbedContract;

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function setConfig(array $attrs): EmbedContract;

    /**
     * Récupération d'une instance de service fourni par Facebook.
     *
     * @param string $url
     *
     * @return EmbedFactoryInterface
     */
    public function facebook(string $url): EmbedFactoryInterface;

    /**
     * Récupération d'une instance de service fourni par Instagram.
     *
     * @param string $url
     *
     * @return EmbedFactoryInterface
     */
    public function instagram(string $url): EmbedFactoryInterface;

    /**
     * Récupération d'une instance de service fourni par Pinterest.
     *
     * @param string $url
     *
     * @return EmbedFactoryInterface
     */
    public function pinterest(string $url): EmbedFactoryInterface;

    /**
     * Récupération d'une instance de service d'une video.
     *
     * @param string $url
     *
     * @return EmbedVideoFactoryInterface
     */
    public function video(string $url): EmbedVideoFactoryInterface;

    /**
     * Récupération d'une instance de service fourni par Vimeo.
     *
     * @param string $url
     *
     * @return EmbedFactoryInterface
     */
    public function vimeo(string $url): EmbedFactoryInterface;

    /**
     * Récupération d'une instance de service fourni par Youtube.
     *
     * @param string $url
     *
     * @return EmbedYoutubeFactoryInterface
     */
    public function youtube(string $url): EmbedYoutubeFactoryInterface;
}
