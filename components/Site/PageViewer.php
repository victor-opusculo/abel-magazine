<?php
namespace VictorOpusculo\AbelMagazine\Components\Site;

use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Pages\Page;
use VictorOpusculo\PComp\Component;

use function VictorOpusculo\PComp\Prelude\rawText;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

class PageViewer extends Component
{
    protected function setUp()
    {
    }

    public Page $page;
    public bool $showTitle = true;


    public static function applyTranslationIfUsed(string $pageContent) : string
    {
        $lang = I18n::$instance->fetchCurrentLang();
        if (mb_ereg('<i18n lang="([A-Za-z_]+)">((\r\n|\r|\n|.)*?)<\/i18n>', $pageContent))
        {
            $subMatches = null;
            if (mb_ereg('<i18n lang="(' . $lang . ')">((\r\n|\r|\n|.)*?)<\/i18n>', $pageContent, $subMatches))
            {
                $translatedContent = $subMatches[2] ?? '';
                return $translatedContent;
            }
            return '';
        }
        return $pageContent;
    }

    protected function markup(): Component|array|null
    {
        return 
        [
            $this->showTitle ? tag('h2', class: 'mb-4', children: text(self::applyTranslationIfUsed($this->page->title->unwrapOr('')))) : null,
            tag('div', children: 
                $this->page->html_enabled->unwrapOr(0) ? 
                rawText(self::applyTranslationIfUsed($this->page->content->unwrapOr(''))) :
                rawText(nl2br(Data::hsc(self::applyTranslationIfUsed($this->page->content->unwrapOr('')))))
            )
        ];
    }
} 