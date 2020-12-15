<?php declare(strict_types=1);

namespace Pollen\Embed\Proxy;

use Pollen\Embed\Contracts\EmbedContract;
use Pollen\Embed\Contracts\EmbedFactoryContract;
use Pollen\Embed\Contracts\EmbedProviderContract;
use Pollen\Embed\Providers\EmbedVideoFactoryInterface;
use Pollen\Embed\Providers\EmbedYoutubeFactoryInterface;
use tiFy\Support\Proxy\AbstractProxy;

/**
 * @method static EmbedProviderContract|null getProvider(string $alias)
 * @method static EmbedFactoryContract|null dispatchFactory(string $url)
 * @method static EmbedContract registerProvider(string $alias, EmbedProviderContract|array $providerDefinition = [])
 * @method static EmbedContract setConfig(array $attrs)
 * @method static EmbedFactoryContract facebook(string $url)
 * @method static EmbedFactoryContract instagram(string $url)
 * @method static EmbedFactoryContract pinterest(string $url)
 * @method static EmbedVideoFactoryInterface video(string $url)
 * @method static EmbedFactoryContract vimeo(string $url)
 * @method static EmbedYoutubeFactoryInterface youtube(string $url)
 */
class Embed extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return EmbedContract|mixed|object
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return EmbedContract::class;
    }
}