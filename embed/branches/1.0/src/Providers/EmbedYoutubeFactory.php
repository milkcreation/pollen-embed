<?php declare(strict_types=1);

namespace Pollen\Embed\Providers;

use RuntimeException;
use Pollen\Embed\Contracts\EmbedProviderContract;
use Pollen\Embed\EmbedBaseFactory;
use tiFy\Support\Proxy\Url;

class EmbedYoutubeFactory extends EmbedBaseFactory implements EmbedYoutubeFactoryInterface
{
    /**
     * Url des données embarquées.
     * @var string
     */
    protected $baseEmbedUrl = 'https://www.youtube.com/embed/';

    /**
     * Identifiant de qualification de la video.
     * @var string|null
     */
    protected $videoId;

    /**
     * @param string $url
     * @param EmbedProviderContract $provider
     */
    public function __construct(string $url, EmbedProviderContract $provider)
    {
        parent::__construct($url, $provider);

        if (!$this->getVideoId()) {
            throw new RuntimeException('Unable to extract Video ID from url argument');
        }
    }

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
            'controls'        => 1,
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
        $baseEmbedUrl = $this->baseEmbedUrl ?? $this->getUrl();

        return Url::set($baseEmbedUrl)->appendSegment($this->getVideoId())->with($this->params()->all())->render();
    }

    /**
     * @inheritDoc
     */
    public function getVideoId(): ?string
    {
        if ($this->videoId === null) {
            $this->videoId = $this->provider()->fetchVideoIdFromUrl($this->getUrl());
        }
        return $this->videoId;
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

    /**
     * {@inheritDoc}
     *
     * @return EmbedYoutubeProviderInterface
     */
    public function provider(): EmbedProviderContract
    {
        return parent::provider();
    }
}