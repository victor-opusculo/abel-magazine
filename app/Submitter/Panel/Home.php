<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter\Panel;

use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Panels\ButtonsContainer;
use VictorOpusculo\AbelMagazine\Components\Panels\FeatureButton;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Helpers\UserTypes;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Home extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('layout.submitterPanel');
    }

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('layout.submitterPanel'))),

            component(ButtonsContainer::class, children:
            [
                component(FeatureButton::class,
                    url: URLGenerator::generatePageUrl("/submitter/panel/articles"),
                    label: I18n::get('pages.myArticles'),
                    iconUrl: URLGenerator::generateFileUrl('assets/pics/page.png')
                )
            ])
        ]);
    }
}