<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Opinions\OpinionId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Panels\ConvenienceLinks;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorOpinion;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Delete extends Component
{

    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.deleteOpinion');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->opinionId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $this->opinion = (new AssessorOpinion([ 'id' => $this->opinionId ]))
            ->getSingle($conn)
            ->fetchArticle($conn);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }

    }

    protected $opinionId;
    private ?AssessorOpinion $opinion = null;

    protected function markup(): Component|array|null
    {
        return isset($this->opinion, $this->opinion->article) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.deleteOpinion'))),

            tag('delete-entity-form',
                delete_function_route_url: URLGenerator::generateFunctionUrl("/admin/panel/opinions/{$this->opinion->id->unwrapOr(0)}"),
                go_back_to_url: "/admin/panel/articles/{$this->opinion->article->id->unwrapOr(0)}",
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson()),
                children:
                [
                    tag('p', class: 'font-bold text-center mb-4', children: text(I18n::get('pages.deleteConfirmation'))),

                    component(Label::class, labelBold: true, label: I18n::get('pages.article'), children: 
                        tag('a', class: 'link', href: URLGenerator::generatePageUrl("/admin/panel/articles/{$this->opinion->article->id->unwrapOr(0)}"), children:
                            text($this->opinion->article->title->unwrapOr(''))
                        )
                    ),
                    component(Label::class, labelBold: true, label: I18n::get('forms.reviewerName'), children: text($this->opinion->assessor_name->unwrapOr(''))),
                    component(Label::class, labelBold: true, label: I18n::get('forms.reviewerEmail'), children: text($this->opinion->assessor_email->unwrapOr(''))),
                    component(Label::class, labelBold: true, label: I18n::get('pages.result'), children: 
                        text($this->opinion->is_approved->unwrapOr(false) ? I18n::get('pages.approved') : I18n::get('pages.disapproved'))
                    ),
                ]
            )
        ])
        : null;
    }
}