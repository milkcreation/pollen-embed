<?php declare(strict_types=1);

namespace Pollen\Embed\Field;

use Pollen\Embed\Contracts\Embed as EmbedManagerContract;
use Pollen\Embed\Contracts\EmbedField as EmbedFieldContract;
use Pollen\Embed\EmbedAwareTrait;
use tiFy\Contracts\Routing\Route;
use tiFy\Field\FieldDriver;
use tiFy\Support\Proxy\Partial;
use tiFy\Support\Proxy\Router;
use tiFy\Support\Proxy\Request;
use tiFy\Validation\Validator as v;

class EmbedField extends FieldDriver implements EmbedFieldContract
{
    use EmbedAwareTrait;

    /**
     * Url de traitement de requête XHR.
     * @var Route|string|null
     */
    private $xhrUrl;

    /**
     * @param EmbedManagerContract $embedManager
     */
    public function __construct(EmbedManagerContract $embedManager)
    {
        $this->setEmbedManager($embedManager);
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        parent::boot();

        $this->xhrUrl = Router::xhr(md5($this->getAlias()), [$this, 'xhrResponse']);
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            /**
             * @var string|null Clé d'indice d'enregistrement des données du fournisseur de service.
             */
            'provider_option_name' => null
        ]);
    }

    /**
     * Récupération de l'url de traitement des requêtes.
     *
     * @param array ...$params
     *
     * @return string
     */
    public function getXhrUrl(...$params): string
    {
        return $this->xhrUrl instanceof Route ? $this->xhrUrl->getUrl($params) : $this->xhrUrl;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set([
            'attrs.data-control' => 'embed-field',
            'attrs.data-options' => [
                'ajax' => [
                    'url'      => $this->getXhrUrl(),
                    'method'   => 'post',
                    'data'     => [],
                    'dataType' => 'json',
                ],
            ],
        ]);

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
        $url = trim(Request::input('value'));

        if (!v::url()->validate($url)) {
            return [
                'success' => false,
                'data'    => Partial::get('notice', [
                    'type'    => 'info',
                    'content' => __('Pas une url', 'tify')
                ])->render()
            ];
        }

        if ($factory = $this->embedManager()->dispatchFactory($url)) {
            return [
                'success' => true,
                'data'    => Partial::get('embed', ['url' => $factory])->render()
            ];
        } else {
            return [
                'success' => false,
                'data'    => Partial::get('notice', [
                    'type'    => 'warning',
                    'content' => __('Impossible de récupéré le contenu associé', 'tify')
                ])->render()
            ];
        }
    }
}