<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles;

use Exception;
use VictorOpusculo\AbelMagazine\Components\Layout\DefaultPageFrame;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\EmailToNotifyNewArticle;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\NotifyAdminFinalArticleUploaded;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\NotifyAuthorArticleApproved;
use VictorOpusculo\PComp\Component;
use VictorOpusculo\PComp\HeadManager;

use function VictorOpusculo\PComp\Prelude\component;
use function VictorOpusculo\PComp\Prelude\tag;
use function VictorOpusculo\PComp\Prelude\text;

final class ChangeNotificationEmail extends Component
{
    protected function setUp()
    {
        HeadManager::$title = I18n::get('pages.changeNotificationEmail');
        $conn = Connection::get();
        try { $this->sett = (new EmailToNotifyNewArticle)->getSingle($conn); }
        catch (Exception) { $this->sett = (new EmailToNotifyNewArticle); }

        try { $this->sett2 = (new NotifyAuthorArticleApproved)->getSingle($conn); }
        catch (Exception) { $this->sett2 = (new NotifyAuthorArticleApproved); }

        try { $this->sett3 = (new NotifyAdminFinalArticleUploaded)->getSingle($conn); }
        catch (Exception) { $this->sett3 = (new NotifyAdminFinalArticleUploaded); }
    
    }

    private ?EmailToNotifyNewArticle $sett = null;
    private ?NotifyAuthorArticleApproved $sett2 = null;
    private ?NotifyAdminFinalArticleUploaded $sett3 = null;

    protected function markup(): Component|array|null
    {
        return component(DefaultPageFrame::class, children:
        [
            tag('h1', children: text(I18n::get('pages.changeNotificationEmail'))),
            tag('set-notification-email-new-article', 
                langJson: Data::hscq(I18n::getFormsTranslationsAsJson()), 
                newArticleEmail: $this->sett->value->unwrapOr(''),
                notifyAuthorArticleApproved: $this->sett2->value->unwrapOr(0),
                notifyAdminFinalArticleUploaded: $this->sett3->value->unwrapOr(0)
            )
        ]);
    }
}