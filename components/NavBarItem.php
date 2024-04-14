<?php
namespace VictorOpusculo\AbelMagazine\Components;

use VictorOpusculo\PComp\{View, StyleManager, Component};
use function VictorOpusculo\PComp\Prelude\{tag, component, text};

class NavBarItem extends Component
{
    protected string $url = "#";
    protected string $label;

    protected function setUp()
    {
       
    } 

    protected function markup() : Component
    {
        return tag('span',
        class: '',
        children:
        [
            tag('a', class: 'hover:bg-indigo-700 font-bold hover:text-white active:bg-indigo-800 cursor-pointer inline-block px-4 py-1' , href: $this->url, children: text($this->label))
        ]);
    }
}