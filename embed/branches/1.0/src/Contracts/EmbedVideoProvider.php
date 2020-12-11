<?php declare(strict_types=1);

namespace Pollen\Embed\Contracts;

interface EmbedVideoProvider extends EmbedProvider
{
    /**
     * {@inheritDoc}
     *
     * @return EmbedVideoFactory
     */
    public function get(string $url): EmbedFactory;

}