<?php
namespace VictorOpusculo\AbelMagazine\Components\Data;

use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\Component;

use function VictorOpusculo\PComp\Prelude\scTag;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

class BasicSearchInput extends Component
{
    public function setUp()
    {
    }

	protected function markup(): Component|array|null
    {
        return tag('form', class: 'my-2', method: 'get', children:
        [
            tag('span', class: 'flex flex-row items-center', children:
            [
                !URLGenerator::$useFriendlyUrls ? scTag('input', type: 'hidden', name: 'page', value: $_GET['page'] ?? '/') : null,
                tag('label', children:
                [
                    text(I18n::get('forms.search') . ': '),
                    scTag('input', type: 'search', class: 'md:w-[300px] w-[calc(100%-80px)]', name: 'q', maxlength: 280, value: Data::hscq($_GET['q'] ?? '')),
                ]),
                tag('button', type: 'submit', class: 'btn min-w-0 ml-2 py-2', children: 
                [
                    scTag('img', alt: 'Pesquisar', src: URLGenerator::generateFileUrl('assets/pics/search.png'))
                ])
            ])
        ]);
    }
}