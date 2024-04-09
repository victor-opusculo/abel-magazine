<?php
namespace VictorOpusculo\AbelMagazine\App\Admin;

use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Login extends Component
{
    protected function setUp()
    {
        HeadManager::$title = "Log-in de administrador";
    }

    protected function markup(): Component|array|null
    {
        return 
        [
            tag('h1', children: text(I18n::get('pages.adminLogin'))),
            tag('admin-login-form', langJson: Data::hscq(I18n::getFormsTranslationsAsJson()))
        ];
    }
}