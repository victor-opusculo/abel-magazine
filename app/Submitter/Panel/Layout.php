<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter\Panel;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Helpers\UserTypes;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Submitters\Submitter;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;

use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Layout extends Component
{
    protected function setUp()
    {
        session_name('abel_magazine_author_user');
        session_start();

        if (empty($_SESSION) || $_SESSION['user_type'] !== UserTypes::author)
        {
            header('location:' . URLGenerator::generatePageUrl('/submitter/login', [ 'messages' => I18n::get('pages.submitterNotLoggedIn') ]), true, 303);
            if (isset($_SESSION)) session_unset();
            session_destroy();
            exit;
        }

        $conn = Connection::get();
        try
        {
            $this->submitter = (new Submitter([ 'id' => $_SESSION['user_id'] ?? 0 ]))
            ->setCryptKey(Connection::getCryptoKey())
            ->getSingle($conn);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    private ?Submitter $submitter = null;

    protected function markup(): Component|array|null
    {
        return isset($this->submitter) ?
        [
            tag('div', class: 'bg-zinc-300 p-2 flex flex-row justify-between items-center', children:
            [
                tag('span', children: text(mb_ereg_replace('{name}', $this->submitter->full_name->unwrapOr(''), I18n::get('layout.submitterLayoutUserName')))),
                tag('span', children:
                [
                    tag('a', class: 'inline-block mr-2 btn', href: URLGenerator::generatePageUrl("/submitter/panel/edit_profile"), children: text(I18n::get('pages.editProfile'))),
                    tag('submitter-logout-button', langJson: Data::hscq(I18n::getFormsTranslationsAsJson()))
                ])
            ]),
            $this->children
        ]
        : null;
    }
}