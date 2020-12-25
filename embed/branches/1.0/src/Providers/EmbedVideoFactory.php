<?php declare(strict_types=1);

namespace Pollen\Embed\Providers;

use Pollen\Embed\Contracts\EmbedProviderContract;
use Pollen\Embed\EmbedBaseFactory;

class EmbedVideoFactory extends EmbedBaseFactory implements EmbedVideoFactoryInterface
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
        return $this->getUrl();
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
    public function isEmbeded(): bool
    {
        return false;
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
     * @return EmbedVideoProviderInterface
     */
    public function provider(): EmbedProviderContract
    {
        return parent::provider();
    }

    /**
     * @inheritDoc
     */
    public function setSource(string $src): EmbedVideoFactoryInterface
    {
        array_push($this->src, $src);

        return $this;
    }
}
