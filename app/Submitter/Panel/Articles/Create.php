<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Edition;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Upload\NotIddedArticleUpload;
use VictorOpusculo\AbelMagazine\Lib\Model\Submitters\Submitter;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Create extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get("pages.newArticle");
        $conn = Connection::get();
        try
        {
            $aEditions = (new Edition)->getAllOpenForSubmittions($conn);
            $this->availableEditions = array_map(fn(Edition $e) =>
            [
                'id' => $e->id->unwrapOr(0),
                'title' => $e->title->unwrapOr('') . " ({$e->edition_label->unwrapOr('')} | "  . ($e->getOtherProperties()->magazineName ?? '') . ")"
            ], $aEditions);
            $this->submitter = (new Submitter([ 'id' => $_SESSION['user_id'] ?? 0 ]))
            ->setCryptKey(Connection::getCryptoKey())
            ->getSingle($conn);

            $langs = I18n::availableLangs() + [ 'es' => 'Español' ];
            $this->availableLangs = array_map(fn($lang, $alias) => [ 'code' => $lang, 'label' => $alias ], array_keys($langs), array_values($langs));
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    private array $availableEditions = [];
    private array $availableLangs = [];
    private ?Submitter $submitter = null;

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.newArticle'))),
            tag('submitter-edit-article', 
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson()),
                availableLanguages: Data::hscq(json_encode($this->availableLangs)),
                availableEditions: Data::hscq(json_encode($this->availableEditions)),
                allowed_mime_types: implode(',', NotIddedArticleUpload::ALLOWED_TYPES),
                authors: Data::hscq(json_encode([ $this->submitter->full_name->unwrapOr('') ]))
            )
        ]);
    }
}