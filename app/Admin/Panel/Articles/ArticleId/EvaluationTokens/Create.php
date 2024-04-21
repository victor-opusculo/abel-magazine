<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\EvaluationTokens;

use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Create extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.createEvTokens');
    }

    protected $articleId;

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.createEvTokens'))),
            tag('create-evaluation-token',
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson()),
                article_id: $this->articleId
            )
        ]);
    }
}