<?php declare(strict_types=1);

namespace Pollen\Embed\Adapters;

use Pollen\Embed\EmbedAwareTrait;
use Pollen\Embed\Contracts\EmbedAdapter;

abstract class AbstractEmbedAdapter implements EmbedAdapter
{
    use EmbedAwareTrait;
}
