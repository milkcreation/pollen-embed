<?php declare(strict_types=1);

namespace Pollen\Embed\Adapters;

use Pollen\Embed\Field\EmbedField;
use tiFy\Contracts\View\PlatesEngine;

class WordpressEmbedField extends EmbedField
{
    public function render(): string
    {
        if ($this->view() instanceof PlatesEngine) {
            $this->view()->addPath($this->embedManager()->resources('/views/field/embed.wp'), null, true);
        }

        return parent::render();
    }
}