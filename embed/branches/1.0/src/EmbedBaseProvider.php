<?php declare(strict_types=1);

namespace Pollen\Embed;

use LogicException;
use Pollen\Embed\Contracts\Embed as EmbedManager;
use Pollen\Embed\Contracts\EmbedFactory as EmbedFactoryContract;
use Pollen\Embed\Contracts\EmbedProvider as EmbedProviderContract;
use tiFy\Support\Concerns\BuildableTrait;
use tiFy\Support\Concerns\ParamsBagTrait;

class EmbedBaseProvider implements EmbedProviderContract
{
    use BuildableTrait, ParamsBagTrait;

    /**
     * Instance du gestionnaire de services.
     * @var EmbedManager
     */
    private $embedManager;

    /**
     * Alias de qualification.
     * @var string|null
     */
    protected $alias = '';

    /**
     * @inheritDoc
     */
    public function build(): EmbedProviderContract
    {
        if ($this->isBuilt()) {
            if (!$this->getAlias()) {
                throw new LogicException('Missing alias');
            } elseif (!$this->embedManager() instanceof EmbedManager) {
                throw new LogicException('Invalid related EmbedManager');
            }

            $this->parseParams();

            $this->setBuilt();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $url): EmbedFactoryContract
    {
        return new EmbedBaseFactory($url, $this);
    }

    /**
     * @inheritDoc
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @inheritDoc
     */
    public function embedManager(): EmbedManager
    {
        return $this->embedManager;
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): EmbedProviderContract
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEmbedManager(EmbedManager $embedManager): EmbedProviderContract
    {
        $this->embedManager = $embedManager;

        return $this;
    }
}