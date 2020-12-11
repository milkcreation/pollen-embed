<?php declare(strict_types=1);

namespace Pollen\Embed;

use LogicException, RuntimeException;
use Embed\Embed as EmbedApi;
use Pollen\Embed\Contracts\Embed as EmbedManagerContract;
use Pollen\Embed\Contracts\EmbedFactory as EmbedFactoryContract;
use Pollen\Embed\Contracts\EmbedProvider as EmbedProviderContract;
use Pollen\Embed\Contracts\EmbedFacebookProvider as EmbedFacebookProviderContract;
use Pollen\Embed\Contracts\EmbedInstagramProvider as EmbedInstagramProviderContract;
use Pollen\Embed\Contracts\EmbedPinterestProvider as EmbedPinterestProviderContract;
use Pollen\Embed\Contracts\EmbedVideoFactory as EmbedVideoFactoryContract;
use Pollen\Embed\Contracts\EmbedVideoProvider as EmbedVideoProviderContract;
use Pollen\Embed\Contracts\EmbedVimeoProvider as EmbedVimeoProviderContract;
use Pollen\Embed\Contracts\EmbedYoutubeFactory as EmbedYoutubeFactoryContract;
use Pollen\Embed\Contracts\EmbedYoutubeProvider as EmbedYoutubeProviderContract;
use Pollen\Embed\Partial\EmbedPartial;
use Psr\Container\ContainerInterface as Container;
use ReflectionClass;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ContainerAwareTrait;
use tiFy\Support\MimeTypes;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Partial;
use tiFy\Support\Proxy\Storage;

class Embed implements EmbedManagerContract
{
    use BootableTrait, ContainerAwareTrait;

    /**
     * Instance de la classe.
     * @var static|null
     */
    private static $instance;

    /**
     * Instance du gestionnaire des ressources
     * @var LocalFilesystem|null
     */
    private $resources;

    /**
     * Instance du gestionnaire de configuration.
     * @var ParamsBag
     */
    private $configBag;

    /**
     * Lise des fournisseurs de services déclarés.
     * @var EmbedProviderContract[]|array
     */
    protected $registeredProviders = [];

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
    public static function instance(): EmbedManagerContract
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        throw new RuntimeException(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): EmbedManagerContract
    {
        if (!$this->isBooted()) {
            Partial::register('embed', new EmbedPartial($this));

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
    public function dispatchFactory(string $url): ?EmbedFactoryContract
    {
        $reflector = new ReflectionClass(EmbedApi::class);
        $ds = DIRECTORY_SEPARATOR;
        $oembedPath = dirname($reflector->getFileName()). "{$ds}resources{$ds}oembed.php";

        if (is_file($oembedPath)) {
            $providers = require_once $oembedPath;

            foreach($providers as $endpoint => $patterns) {
                foreach($patterns as $pattern) {
                    if (preg_match($pattern, $url)) {
                        $oembed = $endpoint;
                        break;
                    }
                }
            }

            if (isset($oembed)) {
                $extractor = (new EmbedApi())->get($url);
                if ($extractor->getOEmbed()->getEndpoint()) {
                    $alias = strtolower($extractor->providerName);
                    if ($provider = $this->getProvider($alias)) {
                        return $provider->get($url)->setDatas($extractor);
                    }
                }
            } else if (MimeTypes::inType($url, 'video')) {
                return $this->video($url);
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getProvider(string $alias): ?EmbedProviderContract
    {
        return $this->registeredProviders[$alias] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function resources(?string $path = null)
    {
        if (!isset($this->resources) || is_null($this->resources)) {
            $this->resources = Storage::local(dirname(__DIR__) . '/resources');
        }
        return is_null($path) ? $this->resources : $this->resources->path($path);
    }

    /**
     * @inheritDoc
     */
    public function registerProvider(string $alias, $providerDefinition = []): EmbedProviderContract
    {
        $params = [];

        if (!$providerDefinition instanceof EmbedProviderContract) {
            $params = $providerDefinition;

            $provider = $this->containerHas(EmbedProviderContract::class)
                ? $this->containerGet(EmbedProviderContract::class) : new EmbedBaseProvider();
        } else {
            $provider = $providerDefinition;
        }

        if (!$provider instanceof EmbedProviderContract) {
            throw new LogicException('Invalid AddonDriver Declaration');
        }

        return $this->registeredProviders[$alias] = $provider
            ->setAlias($alias)
            ->setParams(array_merge($this->config("providers.{$alias}", []), $params))
            ->build();
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $attrs): EmbedManagerContract
    {
        $this->config($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setProvider(string $alias, $providerDefinition = []): EmbedManagerContract
    {
        $this->registerProvider($alias, $providerDefinition);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function facebook(string $url): EmbedFactoryContract
    {
        $provider = $this->getProvider('facebook');

        if ($provider instanceof EmbedFacebookProviderContract) {
            return $provider->get($url);
        }

        throw new RuntimeException('Unavailable Facebook provider');
    }

    /**
     * @inheritDoc
     */
    public function instagram(string $url): EmbedFactoryContract
    {
        $provider = $this->getProvider('instagram');

        if ($provider instanceof EmbedInstagramProviderContract) {
            return $provider->get($url);
        }

        throw new RuntimeException('Unavailable Instagram provider');
    }

    /**
     * @inheritDoc
     */
    public function pinterest(string $url): EmbedFactoryContract
    {
        $provider = $this->getProvider('pinterest');

        if ($provider instanceof EmbedPinterestProviderContract) {
            return $provider->get($url);
        }

        throw new RuntimeException('Unavailable Pinterest provider');
    }

    /**
     * @inheritDoc
     */
    public function video(string $url): EmbedVideoFactoryContract
    {
        $provider = $this->getProvider('video');

        if ($provider instanceof EmbedVideoProviderContract) {
            return $provider->get($url);
        }

        throw new RuntimeException('Unavailable Video provider');
    }

    /**
     * @inheritDoc
     */
    public function vimeo(string $url): EmbedFactoryContract
    {
        $provider = $this->getProvider('vimeo');

        if ($provider instanceof EmbedVimeoProviderContract) {
            return $provider->get($url);
        }

        throw new RuntimeException('Unavailable Vimeo provider');
    }

    /**
     * @inheritDoc
     */
    public function youtube(string $url): EmbedYoutubeFactoryContract
    {
        $provider = $this->getProvider('youtube');

        if ($provider instanceof EmbedYoutubeProviderContract) {
            return $provider->get($url);
        }

        throw new RuntimeException('Unavailable Youtube provider');
    }
}