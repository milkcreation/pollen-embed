<?php

declare(strict_types=1);

namespace Pollen\Embed\Field;

use Exception;
use Pollen\Embed\Contracts\EmbedContract;
use Pollen\Embed\EmbedAwareTrait;
use tiFy\Field\Contracts\FieldContract;
use tiFy\Field\FieldDriver;
use tiFy\Support\Proxy\Request;
use tiFy\Validation\Validator as v;

class EmbedField extends FieldDriver
{
    use EmbedAwareTrait;

    /**
     * @param EmbedContract $embedManager
     * @param FieldContract $fieldManager
     */
    public function __construct(EmbedContract $embedManager, FieldContract $fieldManager)
    {
        $this->setEmbedManager($embedManager);

        parent::__construct($fieldManager);
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(
            parent::defaults(),
            [
                /**
                 * @var array $provider_datas Données relatives au fournisseur de service.
                 */
                'provider_datas' => [
                    /**
                     * @var string|null Clé d'indice d'enregistrement.
                     */
                    'name'  => null,
                    /**
                     * @var mixed|null
                     */
                    'value' => null,
                ],
                /**
                 * Liste des fournisseurs de services autorisés.
                 * @var array
                 */
                'providers'      => [],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set(
            [
                'attrs.data-control' => 'embed-field',
                'attrs.data-options' => [
                    'ajax'           => [
                        'url'      => $this->getXhrUrl(),
                        'method'   => 'post',
                        'data'     => [
                            'providers' => $this->get('providers'),
                        ],
                        'dataType' => 'json',
                    ],
                    'provider_datas' => $this->get('provider_datas'),
                ],
            ]
        );

        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->embedManager()->resources('/views/field/embed');
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
        $url = trim(Request::input('url'));
        $providers = Request::input('providers', []);

        if (!v::url()->validate($url)) {
            return [
                'success' => false,
                'data'    => [
                    'render' => $this->embedManager()->partialManager()->get('embed', ['url' => null])->render(),
                ],
            ];
        }

        try {
            $factory = $this->embedManager()->dispatchFactory($url);

            $datas = ['alias' => $factory->provider()->getAlias()];

            try {
                $oEmbed = $factory->getOEmbed();

                $datas = array_merge($datas, $oEmbed->all(), ['endpoint' => (string)$oEmbed->getEndpoint()]);
            } catch (Exception $e) {
                unset($e);
            }

            return [
                'success' => true,
                'data'    => [
                    'render' => $this->embedManager()->partialManager()->get(
                        'embed',
                        [
                            'url'       => $factory,
                            'providers' => $providers,
                        ]
                    )->render(),
                    'datas'  => $datas,
                ],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data'    => [
                    'render' => $this->embedManager()->partialManager()->get('embed', ['url' => null])->render(),
                ],
            ];
        }
    }
}
