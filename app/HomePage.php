<?php
namespace VictorOpusculo\AbelMagazine\App;

use VictorOpusculo\AbelMagazine\Components\Navigation\TopBar;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\ScriptManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class HomePage extends Component
{
    protected function setUp()
    {
        ScriptManager::registerScript("rpcTest", 
        "
        window.onload = async function()
        {
            const { getAll } = await import(AbelMagazine.functionUrl('/'));
            const arr = await getAll({ qty: 5 });
            console.log(arr);
        }; 
        
        ", "", true);
    }

    protected function markup(): Component|array|null
    {
        return 
        [
            tag('h1', children: text(I18n::get("layout.homePageTitle"))),
        ];
    }
}