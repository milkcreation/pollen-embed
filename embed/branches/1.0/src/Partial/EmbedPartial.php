<?php declare(strict_types=1);

namespace Pollen\Embed\Partial;

use Pollen\Embed\Contracts\Embed as EmbedManagerContract;
use Pollen\Embed\Contracts\EmbedFactory as EmbedFactoryContract;
use tiFy\Partial\PartialDriver as BasePartialDriver;

class EmbedPartial extends BasePartialDriver
{
    /**
     * Instance du gestionnaire de données embarquées.
     * @var EmbedManagerContract
     */
    private $embedManager;

    /**
     * @param EmbedManagerContract $embedManager
     */
    public function __construct(EmbedManagerContract $embedManager)
    {
        $this->embedManager = $embedManager;
    }

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            /**
             * Attributs HTML du champ.
             * @var array $attrs
             */
            'attrs'    => [],
            /**
             * Contenu placé après le champ.
             * @var string $after
             */
            'after'    => '',
            /**
             * Contenu placé avant le champ.
             * @var string $before
             */
            'before'   => '',
            /**
             * Liste des attributs de configuration du pilote d'affichage.
             * @var array $viewer
             */
            'viewer'   => [],
            /**
             * Url|Instance des données embarqués distribuées par le fournisseur de service.
             * @var EmbedFactoryContract|string|null
             * {@internal EmbedFactoryContract recommandé, meilleurs performances.}
             */
            'url'     => null,
            /**
             * Liste des paramètres d'affichage.
             * @var array
             */
            'params' => []
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if (!$url = $this->get('url')) {
            return '';
        } elseif (!$url instanceof EmbedFactoryContract && ($url = $this->embedManager->dispatchFactory($url))) {
            return '';
        }

        $url->setParams($this->get('params', []))->parseParams();

        return $url->getEmbedUrl();

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->embedManager->resources('/views/partial/embed');
    }
}