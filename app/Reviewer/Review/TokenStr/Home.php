<?php
namespace VictorOpusculo\AbelMagazine\App\Reviewer\Review\TokenStr;

use Exception;
use Ramsey\Uuid\Uuid;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorEvaluationToken;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\Context;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\scTag;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class Home extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.reviewArticle');
        $conn = Connection::get();
        try
        {
            if (!Uuid::isValid($this->tokenStr))
                throw new Exception(I18n::get('exceptions.invalidToken'));

            $this->token = (new AssessorEvaluationToken([ 'token' => $this->tokenStr ]))
            ->getSingleFromToken($conn)
            ->fetchArticle($conn);

            $this->token->article->fetchEdition($conn);
            $this->token->article->edition->fetchMagazine($conn);
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $tokenStr;
    private ?AssessorEvaluationToken $token = null;

    protected function markup(): Component|array|null
    {
        return isset($this->token, $this->token->article, $this->token->article->edition, $this->token->article->edition->magazine) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.reviewArticle'))),

            component(Label::class, labelBold: true, label: I18n::get('pages.magazine'), children: text($this->token->article->edition->magazine->name->unwrapOr(''))),

            component(Label::class, labelBold: true, label: I18n::get('forms.title'), children: text($this->token->article->title->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.keywords'), children: text($this->token->article->keywords->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.resume'), lineBreak: true, children: 
                tag('div', class: 'whitespace-pre-line', children:
                    text($this->token->article->resume->unwrapOr(''))
                )
            ),
            component(Label::class, labelBold: true, label: I18n::get('forms.file'), children: 
                tag('a', class: 'btn', href: URLGenerator::generateScriptUrl('fetch_article_nid.php', [ 'id' => $this->token->article->id->unwrapOr(0), 'review_token' => $this->token->token->unwrapOr('') ]), children: text(I18n::get('forms.download')))
            ),

            scTag('hr'),

            tag('article-review-form',
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson()),
                token: $this->tokenStr
            )

        ])
        : null;
    }
}