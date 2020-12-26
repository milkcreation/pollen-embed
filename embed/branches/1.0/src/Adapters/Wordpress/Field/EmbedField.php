<?php declare(strict_types=1);

namespace Pollen\Embed\Adapters\Wordpress\Field;

use Pollen\Embed\Field\EmbedField as BaseEmbedField;
use tiFy\Contracts\View\PlatesEngine;

class EmbedField extends BaseEmbedField
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
