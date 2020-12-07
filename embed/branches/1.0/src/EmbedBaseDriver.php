<?php declare(strict_types=1);

namespace Pollen\Embed;

use Pollen\Embed\Contracts\Embed as EmbedManager;
use Pollen\Embed\Contracts\EmbedDriver as EmbedDriverContract;

class EmbedBaseDriver implements EmbedDriverContract
{
    /**
     * Instance du gestionnaire.
     * @var EmbedManager
     */
    private $embedManager;

    /**
     * @param EmbedManager $embedManager
     */
    public function __construct(EmbedManager $embedManager)
    {
        $this->embedManager = $embedManager;
    }

    /**
     * @inheritDoc
     */
    public function embedManager(): EmbedManager
    {
        return $this->embedManager;
    }
}