<?php declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\Contracts\EmbedProvider as EmbedProviderContract;
use Pollen\Embed\Contracts\EmbedVideoFactory as EmbedVideoFactoryContract;
use Pollen\Embed\Contracts\EmbedVideoProvider as EmbedVideoProviderContract;
use Pollen\Embed\EmbedBaseFactory;

class EmbedVideoFactory extends EmbedBaseFactory implements EmbedVideoFactoryContract
{
    /**
     * Liste des sources.
     * @return string[]
     */
    protected $src = [];

    /**
     * {@inheritDoc}
     * @see https://docs.videojs.com/tutorial-options.html
     */
    public function defaultParams(): array
    {
        return [
            'autoplay'                  => false,
            'controls'                  => true,
            'loop'                      => null,
            'muted'                     => false,
            'poster'                    => null,
            'preload'                   => 'auto',
            'aspectRatio'               => null,
            'autoSetup'                 => null,
            'breakpoints'               => null,
            'children'                  => null,
            'fluid'                     => true,
            'inactivityTimeout'         => null,
            'language'                  => null,
            'languages'                 => null,
            'liveui'                    => false,
            'nativeControlsForTouch'    => null,
            'notSupportedMessage'       => null,
            'fullscreen'                => null,
            'playbackRates'             => null,
            'plugins'                   => null,
            'responsive'                => false,
            'suppressNotSupportedError' => null,
            'techCanOverridePoster'     => null,
            'techOrder'                 => null,
            'userActions'               => null
        ];
    }

    /**
     * @inheritDoc
     */
    public function getEmbedUrl(): string
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function getSources(): array
    {
        if (!in_array($this->url, $this->src)) {
            array_unshift($this->src, $this->url);
        }
        return $this->src;
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): self
    {
        foreach ($this->params() as $key => $value) {
            if ($value === null) {
                $this->params()->forget($key);
            }
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return EmbedVideoProviderContract
     */
    public function provider(): EmbedProviderContract
    {
        return parent::provider();
    }

    /**
     * @inheritDoc
     */
    public function setSource(string $src): EmbedVideoFactoryContract
    {
        array_push($this->src, $src);

        return $this;
    }
}