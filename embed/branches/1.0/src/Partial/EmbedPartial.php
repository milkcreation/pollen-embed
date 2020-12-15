<?php declare(strict_types=1);

namespace Pollen\Embed\Partial;

use Exception;
use Pollen\Embed\Contracts\EmbedContract;
use Pollen\Embed\Contracts\EmbedFactoryContract;
use Pollen\Embed\Contracts\EmbedPartialContract;
use Pollen\Embed\EmbedAwareTrait;
use Pollen\Embed\Providers\EmbedVideoFactoryInterface;
use Pollen\Embed\Providers\EmbedYoutubeFactoryInterface;
use tiFy\Contracts\Partial\Partial as PartialManager;
use tiFy\Partial\PartialDriver as BasePartialDriver;
use tiFy\Support\Proxy\Request;
use tiFy\Validation\Validator as v;

class EmbedPartial extends BasePartialDriver implements EmbedPartialContract
{
    use EmbedAwareTrait;

    /**
     * @param EmbedContract $embedManager
     * @param PartialManager $partialManager
     */
    public function __construct(EmbedContract $embedManager, PartialManager $partialManager)
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
             * @var bool|string
             */
            'defer'      => 'auto',
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
        $defer = $this->get('defer');
        $factory = null;
        $defered = false;

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


        if (!$url = $this->pull('url')) {
            $this->set('provider', 'oops');

            return parent::render();
        } elseif (!$url instanceof EmbedFactoryContract) {
            $url = trim($url);

            if ($defer === false) {
                try {
                    $factory = $this->embedManager()->dispatchFactory($url);
                } catch (Exception $e) {
                    return '';
                }
            } else {
                $defered = true;
            }
        } else {
            $factory = $url;
            if ($defer !== false) {
                $url = $factory->getUrl();
                $defered = true;
            }
        }

        if ($defered) {
            $provider = 'defered';

            $this->set([
                'attrs.class'         => implode(' ', array_filter([$this->get('attrs.class'), 'Embed--defered'])),
                'attrs.data-options'  => [
                    'ajax' => [
                        'method'   => 'post',
                        'url'      => $this->getXhrUrl(),
                        'data'     => [
                            'url'    => $url,
                            'params' => $this->get('params', []),
                            'viewer' => $this->get('viewer'),
                        ],
                        'dataType' => 'json',
                    ],
                ],
                'attrs.data-control'  => 'embed',
                'attrs.data-provider' => $provider,
                'provider'            => $provider,
            ]);
        } elseif ($factory instanceof EmbedFactoryContract) {
            $factory->setParams($this->get('params', []))->parseParams();

            $this->set('attrs.class', implode(' ',
                array_filter([$this->get('attrs.class'), 'Embed--' . $factory->getProviderAlias()])
            ));

            // VIDEO
            if ($factory instanceof EmbedVideoFactoryInterface) {
                $provider = 'video';
                $this->set('attrs.class', implode(' ', array_filter([
                    $this->get('attrs.class'),
                    'video-js vjs-default-skin vjs-big-play-centered',
                ])));

                $this->set([
                    'attrs.data-control'  => 'embed',
                    'attrs.data-provider' => $provider,
                    'attrs.data-params'   => $factory->params()->all(),
                    'provider'            => $provider,
                ]);

                $this->push('attrs', 'controls');

                $sources = $factory->getSources();
                foreach ($sources as $src) {
                    $this->push('sources', [
                        'attrs' => [
                            'class' => null,
                            'src'   => $src,
                        ],
                        'tag'   => 'source',
                    ]);
                }
            }
            // YOUTUBE
            if ($factory instanceof EmbedYoutubeFactoryInterface) {
                $provider = 'youtube';
                $this->set([
                    'attrs.data-provider' => 'youtube',
                    'provider'            => $provider,
                ]);

                if ($factory->params('fs')) {
                    $this->push('attrs', 'allowfullscreen');
                }

                if ($factory->params('enablejsapi')) {
                    $this->set([
                        'attrs.data-control'  => 'embed',
                        'attrs.data-video-id' => $factory->getVideoId(),
                        'attrs.data-params'   => $factory->params()->all(),
                    ]);
                }
            }
        }

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->embedManager()->resources('/views/partial/embed');
    }

    /**
     * Traitement de la requête XHR de récupération d'un contenu embarqué.
     *
     * @param array ...$args
     *
     * @return array
     */
    public function xhrResponse(...$args): array
    {
        $url = Request::input('url');
        $params = Request::input('params', []);
        $this->set('viewer', Request::input('viewer', []));

        if (!v::url()->validate($url)) {
            return [
                'success' => true,
                'data'    => $this->view('oops'),
            ];
        }

        try {
            $factory = $this->embedManager()->dispatchFactory($url);

            return [
                'success' => true,
                'data'    => $this->partialManager()->get('embed', [
                    'url'    => $factory,
                    'params' => $params,
                    'defer'  => false,
                ])->render(),
            ];
        } catch (Exception $e) {
            return [
                'success' => true,
                'data'    => $this->view('oops'),
            ];
        }
    }
}