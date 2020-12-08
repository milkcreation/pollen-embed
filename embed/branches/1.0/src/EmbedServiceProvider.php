<?php declare(strict_types=1);

namespace Pollen\Embed;

use Pollen\Embed\Contracts\Embed as EmbedManagerContract;
use Pollen\Embed\Contracts\EmbedProvider as EmbedProviderContract;
use Pollen\Embed\Contracts\EmbedFacebookProvider as EmbedFacebookProviderContract;
use Pollen\Embed\Contracts\EmbedInstagramProvider as EmbedInstagramProviderContract;
use Pollen\Embed\Contracts\EmbedPinterestProvider as EmbedPinterestProviderContract;
use Pollen\Embed\Contracts\EmbedVimeoProvider as EmbedVimeoProviderContract;
use Pollen\Embed\Contracts\EmbedYoutubeProvider as EmbedYoutubeProviderContract;
use Pollen\Embed\Providers\EmbedFacebookProvider;
use Pollen\Embed\Providers\EmbedInstagramProvider;
use Pollen\Embed\Providers\EmbedPinterestProvider;
use Pollen\Embed\Providers\EmbedVimeoProvider;
use Pollen\Embed\Providers\EmbedYoutubeProvider;
use tiFy\Container\ServiceProvider as BaseServiceProvider;

class EmbedServiceProvider extends BaseServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        EmbedManagerContract::class,
        EmbedProviderContract::class,
        EmbedFacebookProviderContract::class,
        EmbedInstagramProviderContract::class,
        EmbedPinterestProviderContract::class,
        EmbedVimeoProviderContract::class,
        EmbedYoutubeProviderContract::class
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

        $this->registerProviders();
    }

    /**
     * Déclaration des fournisseurs de services.
     *
     * @return void
     */
    public function registerProviders(): void
    {
        $this->getContainer()->add(EmbedProviderContract::class, function (): EmbedProviderContract {
            return new EmbedBaseProvider();
        });

        $this->getContainer()->share(EmbedFacebookProviderContract::class, function (): EmbedFacebookProviderContract {
            return new EmbedFacebookProvider();
        });

        $this->getContainer()->share(EmbedInstagramProviderContract::class, function (): EmbedInstagramProviderContract {
            return new EmbedInstagramProvider();
        });

        $this->getContainer()->share(EmbedPinterestProviderContract::class, function (): EmbedPinterestProviderContract {
            return new EmbedPinterestProvider();
        });

        $this->getContainer()->share(EmbedVimeoProviderContract::class, function (): EmbedVimeoProviderContract {
            return new EmbedVimeoProvider();
        });

        $this->getContainer()->share(EmbedYoutubeProviderContract::class, function (): EmbedYoutubeProviderContract {
            return new EmbedYoutubeProvider();
        });
    }
}
