<?php

declare(strict_types=1);

namespace Pollen\Embed;

use LogicException;
use Pollen\Embed\Contracts\EmbedContract;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ParamsBagTrait;

class EmbedBaseProvider implements EmbedProviderInterface
{
    use BootableTrait;
    use EmbedAwareTrait;
    use ParamsBagTrait;

    /**
     * Alias de qualification.
     * @var string|null
     */
    protected $alias = '';

    /**
     * @param EmbedContract $embedManager
     */
    public function __construct(EmbedContract $embedManager)
    {
        $this->setEmbedManager($embedManager);
    }

    /**
     * @inheritDoc
     */
    public function boot(): EmbedProviderInterface
    {
        if ($this->isBooted()) {
            if (!$this->getAlias()) {
                throw new LogicException('Missing alias');
            } elseif (!$this->embedManager() instanceof EmbedContract) {
                throw new LogicException('Invalid related EmbedManager');
            }

            $this->parseParams();

            $this->setBooted();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $url): EmbedFactoryInterface
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
    public function setAlias(string $alias): EmbedProviderInterface
    {
        $this->alias = $alias;

        return $this;
    }
}
