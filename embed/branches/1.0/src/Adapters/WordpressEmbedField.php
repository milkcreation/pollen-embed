<?php declare(strict_types=1);

namespace Pollen\Embed\Adapters;

use Pollen\Embed\Field\EmbedField;
use tiFy\Contracts\View\PlatesEngine;

class WordpressEmbedField extends EmbedField
{
    public function render(): string
    {
        $viewEngine = $this->view();
        if (($viewEngine instanceof PlatesEngine) && !$viewEngine->getOverrideDir()) {
            $viewEngine->addPath($this->embedManager()->resources('/views/field/embed.wp'));
        }

        return parent::render();
    }
}