<?php declare(strict_types=1);

namespace Pollen\Embed;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use Embed\Embed as EmbedApi;
use Pollen\Embed\Adapters\AdapterInterface;
use Pollen\Embed\Contracts\EmbedContract;
use Pollen\Embed\Field\EmbedField;
use Pollen\Embed\Partial\EmbedPartial;
use Pollen\Embed\Providers\EmbedFacebookProvider;
use Pollen\Embed\Providers\EmbedFacebookProviderInterface;
use Pollen\Embed\Providers\EmbedInstagramProvider;
use Pollen\Embed\Providers\EmbedInstagramProviderInterface;
use Pollen\Embed\Providers\EmbedPinterestProvider;
use Pollen\Embed\Providers\EmbedPinterestProviderInterface;
use Pollen\Embed\Providers\EmbedVideoFactoryInterface;
use Pollen\Embed\Providers\EmbedVideoProvider;
use Pollen\Embed\Providers\EmbedVideoProviderInterface;
use Pollen\Embed\Providers\EmbedVimeoProvider;
use Pollen\Embed\Providers\EmbedVimeoProviderInterface;
use Pollen\Embed\Providers\EmbedYoutubeFactoryInterface;
use Pollen\Embed\Providers\EmbedYoutubeProvider;
use Pollen\Embed\Providers\EmbedYoutubeProviderInterface;
use Psr\Container\ContainerInterface as Container;
use ReflectionClass;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Partial\Partial as PartialManagerContract;
use tiFy\Partial\Partial;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ContainerAwareTrait;
use tiFy\Support\MimeTypes;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Field;
use tiFy\Support\Proxy\Storage;
use tiFy\Routing\UrlFactory;

class Embed implements EmbedContract
{
    use BootableTrait, ContainerAwareTrait;

    /**
     * Instance de la classe.
     * @var static|null
     */
    private static $instance;

    /**
     * Instance du gestionnaire de configuration.
     * @var ParamsBag
     */
    private $configBag;

    /**
     * Instance du gestionnaire des ressources
     * @var LocalFilesystem|null
     */
    private $resources;

    /**
     * Instance de l'adapteur associé
     * @var AdapterInterface|null
     */
    protected $adapter;

    /**
     * Liste des champs par défaut.
     * @var string[][]
     */
    protected $defaultFields = [
        'embed'  => EmbedField::class
    ];

    /**
     * Liste des portions d'affichage par défaut.
     * @var string[][]
     */
    protected $defaultPartials = [
        'embed'  => EmbedPartial::class
    ];

    /**
     * Liste des fournisseurs de services par défaut.
     * @var string[][]
     */
    protected $defaultProviders = [
        'facebook'  => EmbedFacebookProvider::class,
        'instagram' => EmbedInstagramProvider::class,
        'pinterest' => EmbedPinterestProvider::class,
        'video'     => EmbedVideoProvider::class,
        'vimeo'     => EmbedVimeoProvider::class,
        'youtube'   => EmbedYoutubeProvider::class,
    ];

    /**
     * Cartographie des url d'accès aux données oEmbed des fournisseurs de service
     * @var array|null
     */
    protected $oembedEndpointsMap;

    /**
     * Liste des fournisseurs de services déclarés.
     * @var EmbedProviderInterface[]|array
     */
    protected $providers = [];

    /**
     * Liste des définition de fournisseurs de services déclarés.
     * @var EmbedProviderInterface[]|array
     */
    protected $providerDefinitions = [];

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], Container $container = null)
    {
        $this->setConfig($config);

        if (!is_null($container)) {
            $this->setContainer($container);
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * @inheritDoc
     */
    public static function instance(): EmbedContract
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new RuntimeException(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): EmbedContract
    {
        if (!$this->isBooted()) {
            foreach ($this->getDefaultProviders() as $alias => $abstract) {
                $this->registerProvider(
                    $alias,
                    $this->getContainer()->has($abstract) ? $abstract : new $abstract($this)
                );
            }

            foreach ($this->getDefaultFields() as $alias => $abstract) {
                Field::register(
                    'embed',
                    $this->containerHas($abstract) ? $this->containerGet($abstract) : new $abstract($this)
                );
            }

            /** @var PartialManagerContract $partialManager */
            $partialManager = ($this->containerHas(PartialManagerContract::class)
                ? $this->containerGet(PartialManagerContract::class) : Partial::instance()
            );

            foreach ($this->getDefaultPartials() as $alias => $abstract) {
                $partialManager->register(
                    $alias,
                    $this->containerHas($abstract) ? $abstract : new $abstract($this, $partialManager)
                );
            }

            $this->setBooted();
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function config($key = null, $default = null)
    {
        if ($this->configBag === null) {
            $this->configBag = new ParamsBag();
        }

        if (is_string($key)) {
            return $this->configBag->get($key, $default);
        } elseif (is_array($key)) {
            return $this->configBag->set($key);
        } else {
            return $this->configBag;
        }
    }

    /**
     * @inheritDoc
     */
    public function dispatchFactory(string $url): EmbedFactoryInterface
    {
        if ($this->getOEmbedEndpoint($url)) {
            $extractor = (new EmbedApi())->get($url);
            $alias = strtolower($extractor->providerName);

            if ($provider = $this->getProvider($alias)) {
                try {
                    return $provider->get($url)->setDatas($extractor);
                } catch (Exception $e) {
                    throw new RuntimeException($e->getMessage());
                }
            }
        } else {
            if (MimeTypes::inType($url, 'video')) {
                return $this->video($url);
            }
        }
        throw new RuntimeException('Unable to find a match ProviderFactory');
    }

    /**
     * @inheritDoc
     */
    public function getAdapter(): ?AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultFields(): array
    {
        return array_merge($this->defaultFields, $this->getAdapter()->getDefaultFields() ?? []);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultPartials(): array
    {
        return array_merge($this->defaultPartials, $this->getAdapter()->getDefaultPartials() ?? []);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultProviders(): array
    {
        return array_merge($this->defaultProviders, $this->getAdapter()->getDefaultProviders() ?? []);
    }

    /**
     * @inheritDoc
     */
    public function getOEmbedEndpoint(string $url, array $params = []): ?string
    {
        if (is_null($this->oembedEndpointsMap)) {
            try {
                $reflector = new ReflectionClass(EmbedApi::class);
                $ds = DIRECTORY_SEPARATOR;
                $this->oembedEndpointsMap = require_once dirname($reflector->getFileName()) .
                    "{$ds}resources{$ds}oembed.php";
            } catch (Exception $e) {
                $this->oembedEndpointsMap = [];
            }
        }

        foreach ($this->oembedEndpointsMap as $endpoint => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $url)) {
                    return (new UrlFactory($endpoint))->with(['format' => 'json', 'url' => $url])->render();
                }
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getProvider(string $alias): ?EmbedProviderInterface
    {
        if (isset($this->providers[$alias])) {
            return $this->providers[$alias];
        }

        if (!$def = $this->providerDefinitions[$alias] ?? null) {
            throw new InvalidArgumentException(sprintf('EmbedProvider with alias [%s] unavailable', $alias));
        }

        $params = [];

        if (!$def instanceof EmbedProviderInterface) {
            $params = is_array($def) ? $def : [];
            $provider = $this->containerHas($def) ? $this->containerGet($def) : new EmbedBaseProvider($this);
        } else {
            $provider = $def;
        }

        if (!$provider instanceof EmbedProviderInterface) {
            return null;
        }

        return $this->providers[$alias] = $provider
            ->setAlias($alias)
            ->setParams(array_merge($this->config("providers.{$alias}", []), $params))
            ->boot();
    }

    /**
     * @inheritDoc
     */
    public function resources(?string $path = null)
    {
        if (!isset($this->resources) || is_null($this->resources)) {
            $this->resources = Storage::local(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resources');
        }
        return is_null($path) ? $this->resources : $this->resources->path($path);
    }

    /**
     * @inheritDoc
     */
    public function registerProvider(string $alias, $providerDefinition = []): EmbedContract
    {
        $this->providerDefinitions[$alias] = $providerDefinition;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(AdapterInterface $adapter): EmbedContract
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $attrs): EmbedContract
    {
        $this->config($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function facebook(string $url): EmbedFactoryInterface
    {
        $provider = $this->getProvider('facebook');

        if ($provider instanceof EmbedFacebookProviderInterface) {
            return $provider->get($url);
        }

        throw new RuntimeException('Unavailable Facebook provider');
    }

    /**
     * @inheritDoc
     */
    public function instagram(string $url): EmbedFactoryInterface
    {
        $provider = $this->getProvider('instagram');

        if ($provider instanceof EmbedInstagramProviderInterface) {
            return $provider->get($url);
        }

        throw new RuntimeException('Unavailable Instagram provider');
    }

    /**
     * @inheritDoc
     */
    public function pinterest(string $url): EmbedFactoryInterface
    {
        $provider = $this->getProvider('pinterest');

        if ($provider instanceof EmbedPinterestProviderInterface) {
            return $provider->get($url);
        }

        throw new RuntimeException('Unavailable Pinterest provider');
    }

    /**
     * @inheritDoc
     */
    public function video(string $url): EmbedVideoFactoryInterface
    {
        $provider = $this->getProvider('video');

        if ($provider instanceof EmbedVideoProviderInterface) {
            return $provider->get($url);
        }

        throw new RuntimeException('Unavailable Video provider');
    }

    /**
     * @inheritDoc
     */
    public function vimeo(string $url): EmbedFactoryInterface
    {
        $provider = $this->getProvider('vimeo');

        if ($provider instanceof EmbedVimeoProviderInterface) {
            return $provider->get($url);
        }

        throw new RuntimeException('Unavailable Vimeo provider');
    }

    /**
     * @inheritDoc
     */
    public function youtube(string $url): EmbedYoutubeFactoryInterface
    {
        $provider = $this->getProvider('youtube');

        if ($provider instanceof EmbedYoutubeProviderInterface) {
            return $provider->get($url);
        }

        throw new RuntimeException('Unavailable Youtube provider');
    }
}
