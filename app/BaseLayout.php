<?php
namespace VictorOpusculo\AbelMagazine\App;

use VictorOpusculo\AbelMagazine\Components\NavBar;
use VictorOpusculo\AbelMagazine\Components\NavBarItem;
use VictorOpusculo\AbelMagazine\Components\Navigation\TopBar;
use VictorOpusculo\AbelMagazine\Components\PageMessages;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\Component;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\scTag;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class BaseLayout extends Component
{
    protected function markup(): Component|array|null
    {
        return 
        [
            tag('div', class: 'min-h-[calc(100vh-150px)]', children:
            [
                component(TopBar::class),
                tag('header', class: 'bg-indigo-300', children:
                    component(NavBar::class, children:
                    [
                        component(NavBarItem::class, url: URLGenerator::generatePageUrl('/'), label: I18n::get('layout.adminPanelHome')),
                        component(NavBarItem::class, url: URLGenerator::generatePageUrl('/submitter/panel'), label: I18n::get('layout.submitterPanel'))
                    ])
                ),
                component(PageMessages::class),
                $this->children
            ]),

            tag('dialog', id: 'messageBox', class: 'backdrop:backdrop-blur', children:
            [
                    tag('form', method: 'dialog', class: 'text-center min-w-[350px] p-4 dark:text-white dark:bg-zinc-800', children:
                    [
                        tag('h3', class: 'font-bold text-[1.2rem]', id: 'messageBox_title'),
                        tag('p', class: 'my-4', id: 'messageBox_message'),
                        tag('button', value: 'ok', class: 'hidden btn mr-2', children: text('Ok')),
                        tag('button', value: 'cancel', class: 'hidden btn mr-2', children: text('Cancelar')),
                        tag('button', value: 'yes', class: 'hidden btn mr-2', children: text('Sim')),
                        tag('button', value: 'no', class: 'hidden btn', children: text('Não')),
                    ])
            ]),

            tag('footer', class: 'flex flex-col md:flex-row justify-center items-center h-[150px] mt-4 bg-indigo-800 py-4 md:px-8 px-4 text-white', children: 
            [
                scTag('img', class: 'inline-block mr-4', width: 128, src: URLGenerator::generateFileUrl('assets/pics/abel_dark.png')),
                tag('div', children: 
                [
                    text(I18n::get("layout.topBarSiteDescription") . ': '),
                    tag('a', class: 'hover:underline', href: 'http://portalabel.org.br', children: text('Associação Brasileira das Escolas do Legislativo e de Contas'))
                ])
            ])
        ];
    }
}