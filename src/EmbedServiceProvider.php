<?php

declare(strict_types=1);

namespace Pollen\Embed;

use Pollen\Embed\Adapters\WordpressAdapter;
use Pollen\Embed\Contracts\EmbedContract;
use Pollen\Embed\Providers\EmbedFacebookProviderInterface;
use Pollen\Embed\Providers\EmbedInstagramProviderInterface;
use Pollen\Embed\Providers\EmbedPinterestProviderInterface;
use Pollen\Embed\Providers\EmbedVideoProviderInterface;
use Pollen\Embed\Providers\EmbedVimeoProviderInterface;
use Pollen\Embed\Providers\EmbedYoutubeProviderInterface;
use Pollen\Embed\Field\EmbedField;
use Pollen\Embed\Partial\EmbedPartial;
use Pollen\Embed\Providers\EmbedFacebookProvider;
use Pollen\Embed\Providers\EmbedInstagramProvider;
use Pollen\Embed\Providers\EmbedPinterestProvider;
use Pollen\Embed\Providers\EmbedVideoProvider;
use Pollen\Embed\Providers\EmbedVimeoProvider;
use Pollen\Embed\Providers\EmbedYoutubeProvider;
use tiFy\Container\ServiceProvider as BaseServiceProvider;
use tiFy\Field\Contracts\FieldContract;
use tiFy\Partial\Contracts\PartialContract;

class EmbedServiceProvider extends BaseServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        EmbedContract::class,
        EmbedField::class,
        EmbedPartial::class,
        EmbedFacebookProvider::class,
        EmbedInstagramProvider::class,
        EmbedPinterestProvider::class,
        EmbedVideoProvider::class,
        EmbedVimeoProvider::class,
        EmbedYoutubeProvider::class,
        WordpressAdapter::class,
    ];

    /**
     * @inheritDoc
     */
    public function boot()
    {
        events()->listen(
            'wp.booted',
            function () {
                /** @var EmbedContract $embed */
                $embed = $this->getContainer()->get(EmbedContract::class);
                $embed->setAdapter($this->getContainer()->get(WordpressAdapter::class))->boot();
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            EmbedContract::class,
            function (): EmbedContract {
                return new Embed(config('embed', []), $this->getContainer());
            }
        );

        $this->registerAdapters();
        $this->registerFieldDrivers();
        $this->registerPartialDrivers();
        $this->registerProviders();
    }

    /**
     * Déclaration des adapteurs.
     *
     * @return void
     */
    public function registerAdapters(): void
    {
        $this->getContainer()->share(
            WordpressAdapter::class,
            function () {
                return new WordpressAdapter($this->getContainer()->get(EmbedContract::class));
            }
        );
    }

    /**
     * Déclaration des pilotes de champs.
     *
     * @return void
     */
    public function registerFieldDrivers(): void
    {
        $this->getContainer()->add(
            EmbedField::class,
            function () {
                return new EmbedField(
                    $this->getContainer()->get(EmbedContract::class),
                    $this->getContainer()->get(FieldContract::class)
                );
            }
        );
    }

    /**
     * Déclaration des pilotes de portions d'affichage.
     *
     * @return void
     */
    public function registerPartialDrivers(): void
    {
        $this->getContainer()->add(
            EmbedPartial::class,
            function () {
                return new EmbedPartial(
                    $this->getContainer()->get(EmbedContract::class),
                    $this->getContainer()->get(PartialContract::class)
                );
            }
        );
    }

    /**
     * Déclaration des fournisseurs de services.
     *
     * @return void
     */
    public function registerProviders(): void
    {
        $this->getContainer()->share(
            EmbedFacebookProvider::class,
            function (): EmbedFacebookProviderInterface {
                return new EmbedFacebookProvider($this->getContainer()->get(EmbedContract::class));
            }
        );

        $this->getContainer()->share(
            EmbedInstagramProvider::class,
            function (): EmbedInstagramProviderInterface {
                return new EmbedInstagramProvider($this->getContainer()->get(EmbedContract::class));
            }
        );

        $this->getContainer()->share(
            EmbedPinterestProvider::class,
            function (): EmbedPinterestProviderInterface {
                return new EmbedPinterestProvider($this->getContainer()->get(EmbedContract::class));
            }
        );

        $this->getContainer()->share(
            EmbedVideoProvider::class,
            function (): EmbedVideoProviderInterface {
                return new EmbedVideoProvider($this->getContainer()->get(EmbedContract::class));
            }
        );

        $this->getContainer()->share(
            EmbedVimeoProvider::class,
            function (): EmbedVimeoProviderInterface {
                return new EmbedVimeoProvider($this->getContainer()->get(EmbedContract::class));
            }
        );

        $this->getContainer()->share(
            EmbedYoutubeProvider::class,
            function (): EmbedYoutubeProviderInterface {
                return new EmbedYoutubeProvider($this->getContainer()->get(EmbedContract::class));
            }
        );
    }
}
