<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Helpers\UserTypes;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Administrators\Administrator;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Layout extends Component
{
    protected function setUp()
    {
        $conn = Connection::get();
        try
        {
            session_name('abel_magazine_admin_user');
            session_start();

            if ($_SESSION['user_type'] !== UserTypes::administrator)
                throw new Exception(I18n::get('exceptions.wrongUserTypeReqAdmin'));

            $this->administrator = (new Administrator([ 'id' => $_SESSION['user_id'] ?? 0 ]))->getSingle($conn);
        }
        catch (Exception $e)
        {
            if (isset($_SESSION))
            {
                session_unset();
                session_destroy();
            }
            header('location:' . URLGenerator::generatePageUrl('/admin/login', [ 'messages' => $e->getMessage() ]));
        }
    }

    private ?Administrator $administrator = null;

    protected function markup(): Component|array|null
    {
        return isset($this->administrator) ?
        [
            tag('div', class: 'p-2 bg-zinc-300 flex flex-row justify-between items-center', children:
            [
                tag('span', children: text(mb_ereg_replace('{name}', $this->administrator->full_name->unwrapOr('***'), I18n::get("layout.adminLayoutAdminName")))),
                tag('span', children:
                [
                    tag('a', class: 'btn inline-block mr-2', href: URLGenerator::generatePageUrl('/admin/panel'), children: text(I18n::get('layout.adminPanelHome'))),
                    tag('a', class: 'btn inline-block mr-2', href: URLGenerator::generatePageUrl('/admin/panel/edit_profile'), children: text(I18n::get('layout.editProfile'))),
                    tag('admin-logout-button', langJson: Data::hscq(I18n::getFormsTranslationsAsJson()))
                ]),
            ]),
            $this->children
        ] : null;
    }
}