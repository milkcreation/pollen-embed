<?php declare(strict_types=1);

namespace Pollen\Embed;

use Pollen\Embed\Contracts\Embed as EmbedManagerContract;
use Pollen\Embed\Contracts\EmbedVimeoDriver as EmbedVimeoDriverContract;
use Pollen\Embed\Contracts\EmbedYoutubeDriver as EmbedYoutubeDriverContract;
use Pollen\Embed\Driver\EmbedVimeoDriver;
use Pollen\Embed\Driver\EmbedYoutubeDriver;
use tiFy\Container\ServiceProvider as BaseServiceProvider;

class EmbedServiceProvider extends BaseServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        EmbedManagerContract::class
    ];

    /**
     * @inheritDoc
     */
    public function boot()
    {
        events()->listen('wp.booted', function () {
            $this->getContainer()->get(EmbedManagerContract::class)->boot();
        });
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(EmbedManagerContract::class, function (): EmbedManagerContract {
            return new Embed(config('embed', []), $this->getContainer());
        });

        $this->registerDrivers();
    }

    /**
     * Déclaration des pilotes.
     *
     * @return void
     */
    public function registerDrivers(): void
    {
        $this->getContainer()->share(EmbedVimeoDriverContract::class, function (): EmbedVimeoDriverContract {
            return new EmbedVimeoDriver($this->getContainer()->get(EmbedManagerContract::class));
        });

        $this->getContainer()->share(EmbedYoutubeDriverContract::class, function (): EmbedYoutubeDriverContract {
            return new EmbedYoutubeDriver($this->getContainer()->get(EmbedManagerContract::class));
        });
    }
}
