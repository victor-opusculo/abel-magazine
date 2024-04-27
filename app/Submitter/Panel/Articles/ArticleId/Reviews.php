<?php
namespace VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\ArticleId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\DateTimeTranslator;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorOpinion;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Reviews extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.reviewersOpinions');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->articleId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $article = (new Article([ 'id' => $this->articleId, 'submitter_id' => $_SESSION['user_id'] ]))
            ->getSingleFromSubmitter($conn)
            ->fetchEdition($conn)
            ->fetchOpinions($conn);

            $article->edition->fetchMagazine($conn);

            $this->article = $article;
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $articleId;
    private ?Article $article = null;

    protected function markup(): Component|array|null
    {
        return isset($this->article, $this->article->opinions) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.reviewersOpinions'))),

            component(Label::class, labelBold: true, label: I18n::get('pages.article'), children: text($this->article->title->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.magazineEdition'), children: 
                text($this->article->edition->edition_label->unwrapOr('') . ' | ' . $this->article->edition->magazine->name->unwrapOr(''))
            ),

            count($this->article->opinions) > 0 
                ?   array_map(fn(AssessorOpinion $o) => tag('section', class: 'block p-4 mb-2 border rounded border-zinc-500 bg-zinc-200', children:
                    [
                        tag('div', class: 'font-bold mb-2', children: text($o->is_approved->unwrapOr(false) ? I18n::get('pages.approved') : I18n::get('pages.disapproved'))),
                        tag('div', class: 'whitespace-pre-line', children: text($o->feedback_message->unwrapOr(''))),
                        tag('div', class: 'italic mt-2', children: component(DateTimeTranslator::class, utcDateTime: $o->datetime->unwrapOr(null)))
                    ]), $this->article->opinions)
                :   tag('div', class: 'text-center', children: text(I18n::get('pages.noneYet')))
        ])
        : null;
    }
}