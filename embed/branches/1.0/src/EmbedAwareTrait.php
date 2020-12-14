<?php declare(strict_types=1);

namespace Pollen\Embed;

use Exception;
use Pollen\Embed\Contracts\Embed as EmbedManagerContract;

trait EmbedAwareTrait
{
    /**
     * Instance du gestionnaire de données embarquées.
     * @var EmbedManagerContract|null
     */
    private $embedManager;

    /**
     * Récupération de l'instance du gestionnaire de données embarquées.
     *
     * @return EmbedManagerContract|null
     */
    public function embedManager(): ?EmbedManagerContract
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
     * @param EmbedManagerContract $embedManager
     *
     * @return static
     */
    public function setEmbedManager(EmbedManagerContract $embedManager): self
    {
        $this->embedManager = $embedManager;

        return $this;
    }
}
