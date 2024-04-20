<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter\Panel;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\LgpdTermText;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\LgpdTermVersion;
use VictorOpusculo\AbelMagazine\Lib\Model\Submitters\Submitter;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;
use VictorOpusculo\PComp\ScriptManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class EditProfile extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.editProfile');
        $conn = Connection::get();
        try
        {
            $submitter = (new Submitter([ 'id' => $_SESSION['user_id'] ?? 0 ]))
            ->setCryptKey(Connection::getCryptoKey())
            ->getSingle($conn);

            $this->submitter = $submitter;

            ScriptManager::registerScript('timeZonesSelectScript', Data::getTimeZonesToJavascript());

            $this->lgpdTermVersion = (new LgpdTermVersion)->getSingle($conn)->value->unwrapOr(0);
            $this->lgpdTermText = (new LgpdTermText)->getSingle($conn)->value->unwrapOr('');
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    private ?Submitter $submitter = null;
    private int $lgpdTermVersion = 0;
    private string $lgpdTermText = '';

    protected function markup(): Component|array|null
    {
        return isset($this->submitter) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.editProfile'))),
            tag('submitter-change-data-form',
                ...$this->submitter->getValuesForHtmlForm([ 'lgpdtermversion' ]),
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson()),
                lgpdtermversion: $this->lgpdTermVersion,
                children: tag('textarea', name: 'lgpdTerm', class: 'w-full min-h-[calc(100vh-200px)]', readonly: true, children: text($this->lgpdTermText))
            )
        ])
        : null;
    }
}