<?php declare(strict_types=1);

namespace Pollen\Embed\Partial;

use Pollen\Embed\Contracts\Embed as EmbedManagerContract;
use Pollen\Embed\Contracts\EmbedFactory as EmbedFactoryContract;
use Pollen\Embed\Contracts\EmbedPartial as EmbedPartialContract;
use Pollen\Embed\Contracts\EmbedVideoFactory as EmbedVideoFactoryContract;
use Pollen\Embed\Contracts\EmbedYoutubeFactory as EmbedYoutubeFactoryContract;
use Pollen\Embed\EmbedAwareTrait;
use tiFy\Contracts\Partial\Partial as PartialManager;
use tiFy\Partial\PartialDriver as BasePartialDriver;

class EmbedPartial extends BasePartialDriver implements EmbedPartialContract
{
    use EmbedAwareTrait;

    /**
     * @param EmbedManagerContract $embedManager
     * @param PartialManager $partialManager
     */
    public function __construct(EmbedManagerContract $embedManager, PartialManager $partialManager)
    {
        $this->setEmbedManager($embedManager);

        parent::__construct($partialManager);
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            /**
             * Url|Instance des données embarqués distribuées par le fournisseur de service.
             * @var EmbedFactoryContract|string|null
             * {@internal EmbedFactoryContract recommandé, meilleures performances.}
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
            $url = trim($url);
            $factory = $this->embedManager()->dispatchFactory($url);
        } else {
            $factory = $url;
        }

        if (!$factory instanceof EmbedFactoryContract) {
            return '';
        }

        $factory->setParams($this->get('params', []))->parseParams();

        $this->set('attrs.class', implode(' ',
            array_filter([$this->get('attrs.class'), 'Embed--' . $factory->getProviderAlias()])
        ));

        $responsive = !!$this->pull('responsive');
        if ($responsive) {
            $this->set([
                'attrs.style' => 'position:absolute;top:0;left:0;width:100%;height:100%;',
                'responsive'  => true,
            ]);
        }

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

        // VIDEO
        if ($factory instanceof EmbedVideoFactoryContract) {
            $provider = 'video';
            $this->set('attrs.class', implode(' ', array_filter([
                $this->get('attrs.class'),
                'video-js vjs-default-skin vjs-big-play-centered',
            ])));

            $this->set([
                'attrs.data-control'      => 'embed',
                'attrs.data-provider'     => $provider,
                'attrs.data-video-params' => $factory->params()->all(),
            ]);

            $this->push('attrs', 'controls');

            $sources = $factory->getSources();
            foreach ($sources as $src) {
                $this->push('sources', [
                    'attrs' => [
                        'class' => null,
                        'src'   => $src
                    ],
                    'tag'   => 'source',
                ]);
            }
        }

        // YOUTUBE
        if ($factory instanceof EmbedYoutubeFactoryContract) {
            $provider = 'youtube';
            $this->set('attrs.data-provider', 'youtube');

            if ($factory->params('fs')) {
                $this->push('attrs', 'allowfullscreen');
            }

            if ($factory->params('enablejsapi')) {
                $this->set([
                    'attrs.data-control'      => 'embed',
                    'attrs.data-video-id'     => $factory->getVideoId(),
                    'attrs.data-video-params' => $factory->params()->all(),
                ]);
            }
        }

        return $this->view($provider ?? 'index', $this->all());
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->embedManager()->resources('/views/partial/embed');
    }
}