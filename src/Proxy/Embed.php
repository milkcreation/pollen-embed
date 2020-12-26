<?php declare(strict_types=1);

namespace Pollen\Embed\Proxy;

use Pollen\Embed\Contracts\EmbedContract;
use Pollen\Embed\EmbedFactoryInterface;
use Pollen\Embed\EmbedProviderInterface;
use Pollen\Embed\Providers\EmbedVideoFactoryInterface;
use Pollen\Embed\Providers\EmbedYoutubeFactoryInterface;
use tiFy\Support\Proxy\AbstractProxy;

/**
 * @method static EmbedProviderInterface|null getProvider(string $alias)
 * @method static EmbedFactoryInterface|null dispatchFactory(string $url)
 * @method static EmbedContract registerProvider(string $alias, EmbedProviderInterface|array $providerDefinition = [])
 * @method static EmbedContract setConfig(array $attrs)
 * @method static EmbedFactoryInterface facebook(string $url)
 * @method static EmbedFactoryInterface instagram(string $url)
 * @method static EmbedFactoryInterface pinterest(string $url)
 * @method static EmbedVideoFactoryInterface video(string $url)
 * @method static EmbedFactoryInterface vimeo(string $url)
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
