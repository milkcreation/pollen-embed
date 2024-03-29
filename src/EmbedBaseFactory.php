<?php

declare(strict_types=1);

namespace Pollen\Embed;

use BadMethodCallException;
use Exception;
use Error;
use InvalidArgumentException;
use Embed\Embed as DelegateApiDriver;
use Embed\Extractor;
use StdClass;
use tiFy\Support\Concerns\ParamsBagTrait;
use tiFy\Support\Proxy\Url;

/**
 * @mixin DelegateApiDriver
 */
class EmbedBaseFactory implements EmbedFactoryInterface
{
    use ParamsBagTrait;

    /**
     * Instance du fournisseur de service associé.
     * @var EmbedProviderInterface
     */
    private $provider;

    /**
     * Url des données embarquées.
     * @var string
     */
    protected $baseEmbedUrl;

    /**
     * Instance du pilote de délégation de l'api.
     * @var Extractor|object|null
     */
    protected $delegateApiDriver;

    /**
     * Url du service fournis.
     * @var string
     */
    protected $url;

    /**
     * @param string $url
     * @param EmbedProviderInterface $provider
     */
    public function __construct(string $url, EmbedProviderInterface $provider)
    {
        $this->url = $url;
        $this->provider = $provider;
    }

    /**
     * @inheritDoc
     */
    public function __get(string $offset)
    {
        try {
            return $this->delegateApiDriver()->{$offset};
        } catch (Exception $e) {
            throw new InvalidArgumentException(sprintf('Unvavailable [%s] DelegateApiDriver argument', $offset));
        }
    }

    /**
     * @inheritDoc
     */
    public function __call(string $method, array $arguments)
    {
        try {
            return $this->delegateApiDriver()->{$method}(... $arguments);
        } catch (Error $e) {
            throw new BadMethodCallException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function delegateApiDriver(): object
    {
        if ($this->delegateApiDriver === null) {
            $this->delegateApiDriver = $this->isEmbeded()
                ? (new DelegateApiDriver())->get($this->getUrl()) : new StdClass();
        }
        return $this->delegateApiDriver;
    }

    /**
     * @inheritDoc
     */
    public function getEmbedUrl(): string
    {
        $baseEmbedUrl = $this->baseEmbedUrl ?? $this->getUrl();

        return Url::set($baseEmbedUrl)->with($this->params()->all())->render();
    }

    /**
     * @inheritDoc
     */
    public function getOEmbedEndpoint(): ?string
    {
        if (!$endpoint = $this->provider()->embedManager()->getOEmbedEndpoint($this->getUrl())) {
            $endpoint = ($oembed = $this->delegateApiDriver()->getOEmbed()) ? (string)$oembed->getEndpoint() : null;
        }
        return $endpoint;
    }

    /**
     * @inheritDoc
     */
    public function getProviderAlias(): string
    {
        return $this->provider()->getAlias();
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function isEmbeded(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function provider(): EmbedProviderInterface
    {
        return $this->provider;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->provider()->embedManager()->partialManager()->get('embed', ['url' => $this])->render();
    }

    /**
     * @inheritDoc
     */
    public function setDatas($datas): EmbedFactoryInterface
    {
        $this->datas = $datas;

        return $this;
    }
}
