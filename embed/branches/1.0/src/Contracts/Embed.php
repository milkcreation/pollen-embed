<?php declare(strict_types=1);

namespace Pollen\Embed\Contracts;

use Exception;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Support\ParamsBag;

/**
 * @mixin \tiFy\Support\Concerns\BootableTrait
 * @mixin \tiFy\Support\Concerns\ContainerAwareTrait
 */
interface Embed
{
    /**
     * Récupération de l'instance courante.
     *
     * @return static
     *
     * @throws Exception
     */
    public static function instance(): Embed;

    /**
     * Chargement.
     *
     * @return static
     *
     * @throws Exception
     */
    public function boot(): Embed;

    /**
     * Récupération de paramètre|Définition de paramètres|Instance du gestionnaire de paramètre.
     *
     * @param string|array|null $key Clé d'indice du paramètre à récupérer|Liste des paramètre à définir.
     * @param mixed $default Valeur de retour par défaut lorsque la clé d'indice est une chaine de caractère.
     *
     * @return ParamsBag|mixed
     */
    public function config($key = null, $default = null);

    /**
     * Retourne l'instance des données d'un service associé à une url.
     *
     * @param string $url
     *
     * @return EmbedProvider|null
     */
    public function dispatchFactory(string $url): ?EmbedFactory;

    /**
     * Récupération d'un fournisseur de service selon son nom de qualification.
     *
     * @param string $alias
     *
     * @return EmbedProvider|null
     */
    public function getProvider(string $alias): ?EmbedProvider;

    /**
     * Déclaration d'un fournisseur de service.
     *
     * @param string $alias
     * @param EmbedProvider|array $providerDefinition
     *
     * @return EmbedProvider
     */
    public function registerProvider(string $alias, $providerDefinition = []): EmbedProvider;

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
     * @param EmbedAdapter $adapter
     *
     * @return static
     */
    public function setAdapter(EmbedAdapter $adapter): Embed;

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function setConfig(array $attrs): Embed;

    /**
     * Définition d'un fournisseur de service.
     *
     * @param string $alias
     * @param EmbedProvider|array $providerDefinition
     *
     * @return static
     */
    public function setProvider(string $alias, $providerDefinition = []): Embed;

    /**
     * Récupération d'une instance de service fourni par Facebook.
     *
     * @param string $url
     *
     * @return EmbedFactory
     */
    public function facebook(string $url): EmbedFactory;

    /**
     * Récupération d'une instance de service fourni par Instagram.
     *
     * @param string $url
     *
     * @return EmbedFactory
     */
    public function instagram(string $url): EmbedFactory;

    /**
     * Récupération d'une instance de service fourni par Pinterest.
     *
     * @param string $url
     *
     * @return EmbedFactory
     */
    public function pinterest(string $url): EmbedFactory;

    /**
     * Récupération d'une instance de service d'une video.
     *
     * @param string $url
     *
     * @return EmbedVideoFactory
     */
    public function video(string $url): EmbedVideoFactory;

    /**
     * Récupération d'une instance de service fourni par Vimeo.
     *
     * @param string $url
     *
     * @return EmbedFactory
     */
    public function vimeo(string $url): EmbedFactory;

    /**
     * Récupération d'une instance de service fourni par Youtube.
     *
     * @param string $url
     *
     * @return EmbedYoutubeFactory
     */
    public function youtube(string $url): EmbedYoutubeFactory;
}