<?php declare(strict_types=1);

namespace Pollen\Embed\Partial;

use Pollen\Embed\Contracts\Embed as EmbedManagerContract;
use Pollen\Embed\Contracts\EmbedFactory as EmbedFactoryContract;
use Pollen\Embed\Contracts\EmbedYoutubeFactory as EmbedYoutubeFactoryContract;
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
            'attrs'      => [],
            /**
             * Contenu placé après le champ.
             * @var string $after
             */
            'after'      => '',
            /**
             * Contenu placé avant le champ.
             * @var string $before
             */
            'before'     => '',
            /**
             * Liste des attributs de configuration du pilote d'affichage.
             * @var array $viewer
             */
            'viewer'     => [],
            /**
             * Url|Instance des données embarqués distribuées par le fournisseur de service.
             * @var EmbedFactoryContract|string|null
             * {@internal EmbedFactoryContract recommandé, meilleurs performances.}
             */
            'url'        => null,
            /**
             * Liste des paramètres d'affichage.
             * @see \Pollen\Embed\EmbedBaseFactory::defaultParams()
             * @var array
             */
            'params'     => [],
            /**
             * Activation de la video responsive.
             * @var bool
             */
            'responsive' => true,
            /**
             * Largeur
             * @var int
             */
            'width'      => 640,
            /**
             * Hauteur
             * @var int
             */
            'height'     => 360,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if (!$url = $this->pull('url')) {
            return '';
        } elseif (!$url instanceof EmbedFactoryContract) {
            $factory = $this->embedManager->dispatchFactory($url);
        } else {
            $factory = $url;
        }

        $factory->setParams($this->get('params', []))->parseParams();

        $this->set('attrs.class', implode(
            array_filter([$this->get('attrs.class'), 'Embed--' . $factory->getProviderAlias()])
        ));

        $responsive = !!$this->pull('responsive');

        $ratio = 56.25;
        if (($w = $this->get('width')) && ($h = $this->get('height'))) {
            if ($responsive) {
                $ratio = number_format($h / $w * 100, 2);
            }

            $this->set([
                'attrs.width'  => $w,
                'attrs.height' => $h,
            ]);
        }
        $this->set(compact('ratio'));

        $this->set([
            //'attrs.src'         => $factory->getEmbedUrl(),
            'attrs.frameborder' => 0,
        ]);

        if ($responsive) {
            $this->set([
                'attrs.style' => 'position:absolute;top:0;left:0;width:100%;height:100%;',
                'responsive'  => true,
            ]);
        }

        // YOUTUBE
        if ($factory instanceof EmbedYoutubeFactoryContract) {
            if ($factory->params('fs')) {
                $this->push('attrs', 'allowfullscreen');
            }

            if ($factory->params('enablejsapi')) {
                $this->set('attrs.data-control', 'embed');
                $this->set('attrs.data-video-id', $factory->getVideoId());
                $this->set('attrs.data-player-vars', $factory->params()->all());
            }
        }

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