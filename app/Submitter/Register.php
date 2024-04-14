<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\LgpdTermText;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\LgpdTermVersion;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;
use VictorOpusculo\PComp\ScriptManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Register extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.submitterRegister');
        ScriptManager::registerScript('timezonesScript', Data::getTimeZonesToJavascript());

        $conn = Connection::get();
        try
        {
            $this->lgpdTermText = (new LgpdTermText())->getSingle($conn)->value->unwrapOr('');
            $this->lgpdTermVersion = (int)(new LgpdTermVersion())->getSingle($conn)->value->unwrapOr(0);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    private string $lgpdTermText;
    private int $lgpdTermVersion;

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.submitterRegister'))),

            tag('submitter-register-form', 
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson()), 
                lgpdtermversion: $this->lgpdTermVersion,
                children:
                tag('textarea', class: 'w-full h-[calc(100vh-200px)]', readonly: true, children: 
                    text($this->lgpdTermText)
                )    
            )
        ]);
    }
}