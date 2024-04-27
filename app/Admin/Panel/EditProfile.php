<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Administrators\Administrator;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
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
            $admin = (new Administrator([ 'id' => $_SESSION['user_id'] ?? 0 ]))
            ->getSingle($conn);

            $this->admin = $admin;

            ScriptManager::registerScript('timeZonesSelectScript', Data::getTimeZonesToJavascript());

        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    private ?Administrator $admin = null;

    protected function markup(): Component|array|null
    {
        return isset($this->admin) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.editProfile'))),
            tag('admin-change-data-form',
                ...$this->admin->getValuesForHtmlForm(),
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson())
            )
        ])
        : null;
    }
}