<?php
namespace VictorOpusculo\AbelMagazine\App;

use VictorOpusculo\AbelMagazine\Components\Navigation\TopBar;
use VictorOpusculo\PComp\Component;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;

final class BaseLayout extends Component
{
    protected function markup(): Component|array|null
    {
        return 
        [
            tag('div', class: 'min-w-[100vh]', children:
            [
                component(TopBar::class),
                $this->children
            ])
        ];
    }
}