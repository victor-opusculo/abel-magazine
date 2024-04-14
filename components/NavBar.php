<?php
namespace VictorOpusculo\AbelMagazine\Components;

use VictorOpusculo\PComp\{Component, ScriptManager};
use function VictorOpusculo\PComp\Prelude\{tag, component, scTag, text};

class NavBar extends Component
{
    protected function setUp()
    {

    } 

    protected function markup() : Component
    {
        return tag('div', children: $this->children);
    }
}