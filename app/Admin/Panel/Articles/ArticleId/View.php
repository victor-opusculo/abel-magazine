<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId;

use DateTimeZone;
use Exception;
use VictorOpusculo\AbelMagazine\Components\Data\DataGrid;
use VictorOpusculo\AbelMagazine\Components\Data\DataGridIcon;
use VictorOpusculo\AbelMagazine\Components\Data\DateTimeTranslator;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Panels\ConvenienceLinks;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorOpinion;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\ArticleStatus;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class View extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.viewArticle');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->articleId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $this->article = (new Article([ 'id' => $this->articleId ]))
            ->getSingle($conn)
            ->fetchSubmitter($conn)
            ->fetchEdition($conn);

            $opinions = (new AssessorOpinion([ 'article_id' => $this->articleId ]))->getAllFromArticle($conn);
            $this->opinions = array_map(fn(AssessorOpinion $o) =>
            [
                I18n::get('forms.id') => $o->id->unwrapOr(0),
                I18n::get('forms.fullName') => $o->assessor_name->unwrapOr(''),
                I18n::get('forms.email') => $o->assessor_email->unwrapOr(''),
                I18n::get('pages.isApproved') => $o->is_approved->unwrapOr(false)
                                                    ?   new DataGridIcon('assets/pics/check.png', I18n::get('alerts.yes'))
                                                    :   new DataGridIcon('assets/pics/wrong.png', I18n::get('alerts.no')),
                I18n::get('pages.opinionDateTime') => date_create($o->datetime->unwrapOr('now'), new DateTimeZone('UTC'))
                                                        ->setTimezone(new DateTimeZone($_SESSION['user_timezone'] ?? 'America/Sao_Paulo'))
                                                        ->format(I18n::get('pages.dateTimeFormat'))
            ], $opinions);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $articleId;

    private ?Article $article = null;
    private array $opinions = [];

    protected function markup(): Component|array|null
    {
        return isset($this->article) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.viewArticle'))),

            component(Label::class, labelBold: true, label: I18n::get('pages.edition'), children: 
                tag('a', class: 'link', href: URLGenerator::generatePageUrl("/admin/panel/magazines/{$this->article->edition->magazine_id->unwrapOr(0)}/editions/{$this->article->edition->id->unwrapOr(0)}"), children:
                    text($this->article->edition->title->unwrapOr(''))
                )
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.id'), children: text($this->article->id->unwrapOr(0))),
            component(Label::class, labelBold: true, label: I18n::get('pages.submitterAuthor'), children: 
                tag('a', class: 'link', href: URLGenerator::generatePageUrl("/admin/panel/submitters/{$this->article->submitter->id->unwrapOr(0)}"), children:
                    text($this->article->submitter->full_name->unwrapOr(''))
                )    
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.title'), children: text($this->article->title->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.authors'), children: text(implode(', ', json_decode($this->article->authors->unwrapOr('[]'))))),
            component(Label::class, labelBold: true, label: I18n::get('forms.resume'), lineBreak: true, children: 
                tag('div', class: 'whitespace-pre-line', children:
                    text($this->article->resume->unwrapOr(''))                
                )
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.keywords'), children: text($this->article->keywords->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.language'), children: text(I18n::getAlias($this->article->language->unwrapOr('')))),

            component(Label::class, labelBold: true, label: I18n::get('forms.status'), children: text(ArticleStatus::translate($this->article->status->unwrapOr('')))),
            component(Label::class, labelBold: true, label: I18n::get('forms.submissionDate'), children: 
                component(DateTimeTranslator::class, utcDateTime: $this->article->submission_datetime->unwrapOr(null))
            ),

            component(Label::class, labelBold: true, label: I18n::get('forms.file'), children: 
                tag('a', class: 'btn', href: URLGenerator::generateScriptUrl('/fetch_article_nid.php', [ 'id' => $this->article->id->unwrapOr(0) ]), children: text(I18n::get('pages.view')))
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.iddedFile'), children: 
                $this->article->idded_file_extension->unwrapOr(null)
                ?   tag('a', class: 'btn', href: URLGenerator::generateScriptUrl('/fetch_article.php', [ 'id' => $this->article->id->unwrapOr(0) ]), children: text(I18n::get('pages.view')))
                :   text('-')
            ),

            tag('div', class: 'ml-2', children:
            [
                tag('span', class: 'font-bold mr-2', children: text(I18n::get('pages.changeStatus') . ': ')),
                tag('change-article-status', article_id: $this->article->id->unwrapOr(0), to_status: Data::hscq(ArticleStatus::EvaluationInProgress->value), label: Data::hscq(ArticleStatus::translate(ArticleStatus::EvaluationInProgress)), error: I18n::get('forms.errorChangingStatus')),
                tag('change-article-status', article_id: $this->article->id->unwrapOr(0), to_status: Data::hscq(ArticleStatus::EvaluationInProgress2->value), label: Data::hscq(ArticleStatus::translate(ArticleStatus::EvaluationInProgress2)), error: I18n::get('forms.errorChangingStatus')),
                tag('change-article-status', article_id: $this->article->id->unwrapOr(0), to_status: Data::hscq(ArticleStatus::Approved->value), label: Data::hscq(ArticleStatus::translate(ArticleStatus::Approved)), error: I18n::get('forms.errorChangingStatus')),
                tag('change-article-status', article_id: $this->article->id->unwrapOr(0), to_status: Data::hscq(ArticleStatus::Disapproved->value), label: Data::hscq(ArticleStatus::translate(ArticleStatus::Disapproved)), error: I18n::get('forms.errorChangingStatus')),
                
                $this->article->idded_file_extension->unwrapOr(null)
                    ?   tag('change-article-status', article_id: $this->article->id->unwrapOr(0), to_status: Data::hscq(ArticleStatus::ApprovedWithIddedFile->value), label: Data::hscq(ArticleStatus::translate(ArticleStatus::ApprovedWithIddedFile)), error: I18n::get('forms.errorChangingStatus'))
                    :   null
            ]),

            
            tag('div', class: 'ml-2 my-2', children: 
            [
                tag('span', class: 'mr-2', children:
                [
                    tag('span', class: 'font-bold', children: text(I18n::get('pages.publicationStatus') . ': ')),
                    $this->article->is_approved->unwrapOr(false)
                        ?   text(I18n::get('pages.published'))
                        :   text(I18n::get('pages.notPublished'))
                ]),
                
                $this->article->status->unwrapOr('') === ArticleStatus::ApprovedWithIddedFile->value && $this->article->idded_file_extension->unwrapOr(null)
                    ?    tag('change-article-approvation-status',
                            error: I18n::get('forms.errorChangingStatus'),
                            article_id: $this->article->id->unwrapOr(0),
                            label_approve: !$this->article->is_approved->unwrapOr(false) ? I18n::get('pages.publishArticle') : '',
                            label_disapprove: $this->article->is_approved->unwrapOr(false) ? I18n::get('pages.unpublishArticle') : ''
                        )
                    : null
            ]),

            component(ConvenienceLinks::class,
                editUrl: URLGenerator::generatePageUrl("/admin/panel/articles/{$this->article->id->unwrapOr(0)}/edit"),
                deleteUrl: URLGenerator::generatePageUrl("/admin/panel/articles/{$this->article->id->unwrapOr(0)}/delete")
            ),

            tag('h2', children: text(I18n::get('pages.reviewersOpinions'))),
            
            tag('div', class: 'my-2', children:
            [
                tag('a', class: 'btn mr-2', href: URLGenerator::generatePageUrl("/admin/panel/articles/{$this->article->id->unwrapOr(0)}/evaluation_tokens"), children: text(I18n::get('pages.existentEvTokens'))),
                tag('a', class: 'btn', href: URLGenerator::generatePageUrl("/admin/panel/articles/{$this->article->id->unwrapOr(0)}/evaluation_tokens/create"), children: text(I18n::get('pages.createEvTokens'))),
            ]),

            component(DataGrid::class,
                dataRows: $this->opinions,
                detailsButtonURL: URLGenerator::generatePageUrl("/admin/panel/opinions/{param}"),
                deleteButtonURL: URLGenerator::generatePageUrl("/admin/panel/opinions/{param}/delete"),
                rudButtonsFunctionParamName: I18n::get('forms.id')
            )

        ])
        : null;
    }
}