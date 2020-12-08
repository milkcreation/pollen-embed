<?php declare(strict_types=1);

namespace Pollen\Embed;

use BadMethodCallException, Exception, InvalidArgumentException;
use Embed\Embed as DelegateApiDriver;
use Embed\Extractor;
use Pollen\Embed\Contracts\EmbedFactory as EmbedFactoryContract;
use Pollen\Embed\Contracts\EmbedProvider as EmbedProviderContract;
use tiFy\Support\Concerns\ParamsBagTrait;
use tiFy\Support\Proxy\Partial;
use tiFy\Support\Proxy\Url;

/**
 * @mixin DelegateApiDriver
 */
class EmbedBaseFactory implements EmbedFactoryContract
{
    use ParamsBagTrait;

    /**
     * Liste des données associés.
     * @var Extractor|object|array
     */
    private $datas;

    /**
     * Instance du fournisseur de service associé.
     * @var EmbedProviderContract
     */
    private $provider;

    /**
     * Url des données embarquées.
     * @var string
     */
    protected $baseEmbedUrl;

    /**
     * Instance du pilote de délégation de l'api.
     * @var object|null
     */
    protected $delegateApiDriver;

    /**
     * Url du service fournis.
     * @var string
     */
    protected $url;

    /**
     * @param string $url
     * @param EmbedProviderContract $provider
     */
    public function __construct(string $url, EmbedProviderContract $provider)
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
            return $this->delegateApiDriver()->get($this->url)->{$offset};
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
            return $this->delegateApiDriver()->get($this->url)->{$method}(... $arguments);
        } catch (Exception $e) {
            throw new BadMethodCallException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function delegateApiDriver(): object
    {
        if ($this->delegateApiDriver === null) {
            $this->delegateApiDriver = new DelegateApiDriver();
        }

        return $this->delegateApiDriver;
    }

    /**
     * @inheritDoc
     */
    public function getEmbedUrl(): string
    {
        $baseEmbedUrl = $this->baseEmbedUrl ?? $this->url;

        return Url::set($baseEmbedUrl)->with($this->params()->all())->render();
    }

    /**
     * @inheritDoc
     */
    public function getDatas()
    {
        if ($this->datas === null) {
            return $this->datas = $this->delegateApiDriver()->get($this->url);
        }

        return $this->datas;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return Partial::get('embed', ['url' => $this])->render();
    }

    /**
     * @inheritDoc
     */
    public function setDatas($datas): EmbedFactoryContract
    {
        $this->datas = $datas;

        return $this;
    }
}
