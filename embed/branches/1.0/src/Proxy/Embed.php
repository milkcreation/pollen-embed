<?php declare(strict_types=1);

namespace Pollen\Embed\Proxy;

use Pollen\Embed\Contracts\Embed as EmbedManager;
use Pollen\Embed\Contracts\EmbedFactory;
use Pollen\Embed\Contracts\EmbedProvider;
use tiFy\Support\Proxy\AbstractProxy;

/**
 * @method static EmbedProvider|null getProvider(string $alias)
 * @method static EmbedFactory|null dispatchFactory(string $url)
 * @method static EmbedProvider registerProvider(string $alias, EmbedProvider|array $providerDefinition = [])
 * @method static EmbedManager setConfig(array $attrs)
 * @method static EmbedManager setProvider(array $attrs)
 * @method static EmbedFactory facebook(string $url)
 * @method static EmbedFactory instagram(string $url)
 * @method static EmbedFactory pinterest(string $url)
 * @method static EmbedFactory vimeo(string $url)
 * @method static EmbedFactory youtube(string $url)
 */
class Embed extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return mixed|object|EmbedManager
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
        return EmbedManager::class;
    }
}