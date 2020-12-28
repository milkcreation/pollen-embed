<?php

declare(strict_types=1);

namespace Pollen\Embed;

use Exception;
use Pollen\Embed\Contracts\EmbedContract;

trait EmbedAwareTrait
{
    /**
     * Instance du gestionnaire de données embarquées.
     * @var EmbedContract|null
     */
    private $embedManager;

    /**
     * Récupération de l'instance du gestionnaire de données embarquées.
     *
     * @return EmbedContract|null
     */
    public function embedManager(): ?EmbedContract
    {
        if (is_null($this->embedManager)) {
            try {
                $this->embedManager = Embed::instance();
            } catch (Exception $e) {
                $this->embedManager;
            }
        }

        return $this->embedManager;
    }

    /**
     * Définition du gestionnaire de données embarquées.
     *
     * @param EmbedContract $embedManager
     *
     * @return static
     */
    public function setEmbedManager(EmbedContract $embedManager): self
    {
        $this->embedManager = $embedManager;

        return $this;
    }
}
