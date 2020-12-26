<?php declare(strict_types=1);

namespace Pollen\Embed\Adapters;

use Pollen\Embed\EmbedAwareTrait;
use Pollen\Embed\Contracts\EmbedContract;
use Pollen\Embed\Adapters\Wordpress\Field\EmbedField;

abstract class AbstractEmbedAdapter implements AdapterInterface
{
    use EmbedAwareTrait;

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
    protected $defaultPartials = [];

    /**
     * Liste des fournisseurs de services par défaut.
     * @var string[][]
     */
    protected $defaultProviders = [];

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
    public function getDefaultFields(): array
    {
        return $this->defaultFields;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultPartials(): array
    {
        return $this->defaultPartials;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultProviders(): array
    {
        return $this->defaultProviders;
    }
}
