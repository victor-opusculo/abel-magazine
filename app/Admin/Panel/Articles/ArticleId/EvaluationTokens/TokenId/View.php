<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\EvaluationTokens\TokenId;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Label;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Components\Panels\ConvenienceLinks;
use VictorOpusculo\AbelMagazine\Lib\Helpers\System;
use VictorOpusculo\AbelMagazine\Lib\Helpers\URLGenerator;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorEvaluationToken;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
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
        HeadManager::$title = I18n::get('pages.token');
        $conn = Connection::get();
        try
        {
            if (!Connection::isId($this->tokenId))
                throw new Exception(I18n::get('exceptions.invalidId'));

            $token = (new AssessorEvaluationToken([ 'id' => $this->tokenId ]))->getSingle($conn);
            $this->token = $token;
        }
        catch (Exception $e)
        {
            Context::getRef('page_messages')[] = $e->getMessage();
        }
    }

    protected $tokenId;
    private ?AssessorEvaluationToken $token = null;

    protected function markup(): Component|array|null
    {
        return isset($this->token) ? component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.token'))),
            component(Label::class, labelBold: true, label: I18n::get('forms.reviewerName'), children: text($this->token->assessor_name->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('forms.reviewerEmail'), children: text($this->token->assessor_email->unwrapOr(''))),
            component(Label::class, labelBold: true, label: I18n::get('pages.reviewDirectLink'), children: 
                tag('a', class: 'link', href: URLGenerator::generatePageUrl("/reviewer/review/{$this->token->token->unwrap()}"), children: 
                text(System::getHttpProtocolName() . "://" . $_SERVER["HTTP_HOST"] . URLGenerator::generatePageUrl("/reviewer/review/{$this->token->token->unwrap()}"))
                )
            ),

            component(ConvenienceLinks::class, deleteUrl: URLGenerator::generatePageUrl("/admin/panel/articles/{$this->token->article_id->unwrapOr(0)}/evaluation_tokens/{$this->token->id->unwrapOr(0)}/delete"))
        ])
        : null;
    }
}