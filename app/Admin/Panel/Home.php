<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel;

use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Panels\ButtonsContainer;
use VictorOpusculo\AbelMagazine\Components\Panels\FeatureButton;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
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
        HeadManager::$title = I18n::get('pages.adminHomeTitle');
    }

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children: 
        [
            tag('h1', children: text(I18n::get('pages.adminHomeTitle'))),

            component(ButtonsContainer::class, children:
            [
                component(FeatureButton::class,
                    url: URLGenerator::generatePageUrl('/admin/panel/media'),
                    label: I18n::get('pages.media'),
                    iconUrl: URLGenerator::generateFileUrl('assets/pics/media.png')
                ),
                component(FeatureButton::class,
                    url: URLGenerator::generatePageUrl('/admin/panel/magazines'),
                    label: I18n::get('pages.journals'),
                    iconUrl: URLGenerator::generateFileUrl('assets/pics/journal.svg')
                )
            ])
        ]);
    }
}