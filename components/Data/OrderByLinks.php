<?php
namespace VictorOpusculo\AbelMagazine\Components\Data;

use VictorOpusculo\AbelMagazine\Lib\Helpers\QueryString;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\Component;
use function VictorOpusculo\PComp\Prelude\{ tag, text };

class OrderByLinks extends Component
{
    public function setUp()
    {
    }

    protected array $linksDefinitions;

	protected function markup(): Component|array|null
    {
        return tag('div', class: 'text-right my-2', children:
        [
            tag('span', children: [ text(I18n::get('forms.orderBy') . ': ') ]),
            ...array_map(fn($label, $value) => 
                tag('a', class: 'link text-lg', href: '?' . QueryString::getQueryStringForHtmlExcept('order_by') . QueryString::formatNew('order_by', $value), children: [ text($label) ]),
                array_keys($this->linksDefinitions),
                array_values($this->linksDefinitions)
            )
        ]);
    }
}