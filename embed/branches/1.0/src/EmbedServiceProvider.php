<?php declare(strict_types=1);

namespace Pollen\Embed;

use Pollen\Embed\Adapters\WordpressAdapter;
use Pollen\Embed\Contracts\Embed as EmbedManagerContract;
use Pollen\Embed\Contracts\EmbedFacebookProvider as EmbedFacebookProviderContract;
use Pollen\Embed\Contracts\EmbedInstagramProvider as EmbedInstagramProviderContract;
use Pollen\Embed\Contracts\EmbedPartial as EmbedPartialContract;
use Pollen\Embed\Contracts\EmbedPinterestProvider as EmbedPinterestProviderContract;
use Pollen\Embed\Contracts\EmbedProvider as EmbedProviderContract;
use Pollen\Embed\Contracts\EmbedVideoProvider as EmbedVideoProviderContract;
use Pollen\Embed\Contracts\EmbedVimeoProvider as EmbedVimeoProviderContract;
use Pollen\Embed\Contracts\EmbedYoutubeProvider as EmbedYoutubeProviderContract;
use Pollen\Embed\Contracts\EmbedField as EmbedFieldContract;
use Pollen\Embed\Contracts\WordpressAdapter as WordpressAdapterContract;
use Pollen\Embed\Field\EmbedField;
use Pollen\Embed\Partial\EmbedPartial;
use Pollen\Embed\Providers\EmbedFacebookProvider;
use Pollen\Embed\Providers\EmbedInstagramProvider;
use Pollen\Embed\Providers\EmbedPinterestProvider;
use Pollen\Embed\Providers\EmbedVideoProvider;
use Pollen\Embed\Providers\EmbedVimeoProvider;
use Pollen\Embed\Providers\EmbedYoutubeProvider;
use tiFy\Contracts\Partial\Partial as PartialManager;
use tiFy\Container\ServiceProvider as BaseServiceProvider;

class EmbedServiceProvider extends BaseServiceProvider
{
    /**
     * Liste des fournisseurs de services par défaut.
     * @var string[][]
     */
    protected $defaultProviders = [
        'facebook'  => EmbedFacebookProviderContract::class,
        'instagram' => EmbedInstagramProviderContract::class,
        'pinterest' => EmbedPinterestProviderContract::class,
        'video'     => EmbedVideoProviderContract::class,
        'vimeo'     => EmbedVimeoProviderContract::class,
        'youtube'   => EmbedYoutubeProviderContract::class,
    ];

    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        EmbedManagerContract::class,
        EmbedProviderContract::class,
        EmbedFacebookProviderContract::class,
        EmbedFieldContract::class,
        EmbedInstagramProviderContract::class,
        EmbedPartialContract::class,
        EmbedPinterestProviderContract::class,
        EmbedVideoProviderContract::class,
        EmbedVimeoProviderContract::class,
        EmbedYoutubeProviderContract::class,
        WordpressAdapterContract::class
    ];

    /**
     * @inheritDoc
     */
    public function boot()
    {
        events()->listen('wp.booted', function () {
            $this->getContainer()->get(EmbedManagerContract::class)
                ->setAdapter($this->getContainer()->get(WordpressAdapterContract::class))->boot();
        });
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(EmbedManagerContract::class, function (): EmbedManagerContract {
            $embed = new Embed(config('embed', []), $this->getContainer());

            foreach ($this->defaultProviders as $alias => $abstract) {
                if ($this->getContainer()->has($abstract)) {
                    $embed->setProvider($alias, $this->getContainer()->get($abstract));
                }
            }
            return $embed;
        });

        $this->registerAdapters();
        $this->registerFields();
        $this->registerPartials();
        $this->registerProviders();
    }

    /**
     * Déclaration des adapteurs.
     *
     * @return void
     */
    public function registerAdapters(): void
    {
        $this->getContainer()->share(WordpressAdapterContract::class, function (): WordpressAdapterContract {
            return new WordpressAdapter();
        });
    }

    /**
     * Déclaration des pilotes de champs.
     *
     * @return void
     */
    public function registerFields(): void
    {
        $this->getContainer()->add(EmbedFieldContract::class, function (): EmbedFieldContract {
            return new EmbedField($this->getContainer()->get(EmbedManagerContract::class));
        });
    }

    /**
     * Déclaration des pilotes de portions d'affichage.
     *
     * @return void
     */
    public function registerPartials(): void
    {
        $this->getContainer()->add(EmbedPartialContract::class, function (): EmbedPartialContract {
            return new EmbedPartial(
                $this->getContainer()->get(EmbedManagerContract::class),
                $this->getContainer()->get(PartialManager::class)
            );
        });
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

        $this->getContainer()->share(EmbedVideoProviderContract::class, function (): EmbedVideoProviderContract {
            return new EmbedVideoProvider();
        });

        $this->getContainer()->share(EmbedVimeoProviderContract::class, function (): EmbedVimeoProviderContract {
            return new EmbedVimeoProvider();
        });

        $this->getContainer()->share(EmbedYoutubeProviderContract::class, function (): EmbedYoutubeProviderContract {
            return new EmbedYoutubeProvider();
        });
    }
}
