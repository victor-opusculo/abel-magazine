<?php
namespace VictorOpusculo\AbelMagazine\Components\Panels;

use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\Component;
use function VictorOpusculo\PComp\Prelude\{ tag, text };

class ConvenienceLinks extends Component
{
    public function setUp() { }

    protected ?string $editUrl = null;
    protected ?string $deleteUrl = null;

    protected function markup(): Component|array|null
    {
        return tag('div', class: 'w-full text-left p-2', children:
        [
            $this->editUrl ? tag('a', class: 'link text-lg mr-2', href: $this->editUrl, children: [ text(I18n::get('forms.edit')) ]) : null,
            $this->deleteUrl ? tag('a', class: 'link text-lg', href: $this->deleteUrl, children: [ text(I18n::get('forms.delete')) ]) : null
        ]);
    }
}