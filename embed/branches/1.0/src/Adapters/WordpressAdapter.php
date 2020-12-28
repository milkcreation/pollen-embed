<?php

declare(strict_types=1);

namespace Pollen\Embed\Adapters;

use tiFy\Field\Contracts\FieldContract;
use Pollen\Embed\Adapters\Wordpress\Field\EmbedField;
use Pollen\Embed\Contracts\EmbedContract;

class WordpressAdapter extends AbstractEmbedAdapter
{
    /**
     * Liste des champs par défaut.
     * @var string[][]
     */
    protected $defaultFields = [
        'embed' => EmbedField::class,
    ];

    /**
     * @param EmbedContract $embedManager
     */
    public function __construct(EmbedContract $embedManager)
    {
        parent::__construct($embedManager);

        if ($container = $this->embedManager()->getContainer()) {
            $container->add(EmbedField::class, function () {
                return new EmbedField($this->embedManager(), $this->embedManager()->containerGet(FieldContract::class));
            });
        }
    }
}
