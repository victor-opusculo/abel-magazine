<?php
namespace VictorOpusculo\AbelMagazine\Components\Navigation;

use VictorOpusculo\AbelMagazine\Lib\Helpers\QueryString;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\Component;

use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

class TopBar extends Component
{
    protected function setUp()
    {
        $langAndAliases = I18n::availableLangs();
        $this->langs = array_keys($langAndAliases);
        $this->aliases = array_values($langAndAliases);
    }

    private array $langs = [];
    private array $aliases = [];

    protected function markup(): Component|array|null
    {
        return tag('div', class: 'flex flex-row justify-between p-2 bg-indigo-800', children: 
        [
            tag('span', class: 'uppercase text-white', children: text(I18n::get("layout.topBarSiteDescription"))),
            tag('span', children: 
                array_map(
                    fn($lang, $alias) => tag('a', class: 'mr-4 text-white hover:brightness-90 active:brightness-70', href: URLGenerator::generatePageUrl($_GET['page'] ?? '/', [ ...QueryString::getQueryStringAsArrayExcept('page'), 'change_lang' => $lang ]), children: text($alias)),
                    $this->langs, $this->aliases
                )
            )
        ]);
    }
}