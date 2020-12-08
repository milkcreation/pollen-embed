<?php declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\EmbedBaseFactory;
use tiFy\Support\Proxy\Url;

class EmbedYoutubeFactory extends EmbedBaseFactory
{
    /**
     * Url des données embarquées.
     * @var string
     */
    protected $baseEmbedUrl = 'https://www.youtube.com/embed/';

    /**
     * {@inheritDoc}
     * @see https://developers.google.com/youtube/player_parameters
     */
    public function defaultParams(): array
    {
        return [
            'autoplay'        => 0,
            'cc_lang_pref'    => null,
            'cc_lang_policy'  => null,
            'color'           => null,
            'controls'        => 0,
            'disablekb'       => 0,
            'enablejsapi'     => 1,
            'end'             => null,
            'fs'              => 1,
            'hl'              => null,
            'iv_load_policy'  => 1,
            'list'            => null,
            'listType'        => null,
            'loop'            => 0,
            'modestbranding'  => null,
            'origin'          => null,
            'playlist'        => null,
            'playsinline'     => 0,
            'rel'             => 1,
            'start'           => null,
            'widget_referrer' => null,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getEmbedUrl(): string
    {
        $baseEmbedUrl = $this->baseEmbedUrl ?? $this->url;

        return Url::set($baseEmbedUrl)->with($this->params()->all())->render();
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): self
    {
        foreach($this->params() as $key => $value) {
            if ($value === null) {
                $this->params()->forget($key);
            }
        }
        return $this;
    }
}