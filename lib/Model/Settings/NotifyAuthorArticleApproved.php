<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Settings;

use PHPMailer\PHPMailer\PHPMailer;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\AbelMagazine\Lib\Model\Submitters\Submitter;
use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Option;

/**
 * @property Option<string> name
 * @property Option<string> value
 */
final class NotifyAuthorArticleApproved extends DataEntity
{
    public function __construct($initialValues = null)
    {
        $this->properties = (object)
        [
            'name' => new DataProperty(null, fn() => 'NOTIFY_AUTHOR_ARTICLE_GETS_APPROVED', DataProperty::MYSQL_STRING),
            'value' => new DataProperty(null, fn() => null, DataProperty::MYSQL_STRING)
        ];

        parent::__construct($initialValues);

        $this->properties->name->setValue(Option::some('NOTIFY_AUTHOR_ARTICLE_GETS_APPROVED'));
        $this->properties->value->valueTransformer = [ Data::class, 'booleanTransformer' ];
    }

    protected string $databaseTable = 'settings';
    protected string $formFieldPrefixName = '...';
    protected array $primaryKeys = ['name'];
    protected array $setPrimaryKeysValue = ['name'];

    public function sendEmail(Article $article, Submitter $submitter) : void
    {
        if (!(bool)(int)$this->value->unwrapOr(0))
            return;

        $configs = Data::getMailConfigs();
        $mail = new PHPMailer();

        $mail->IsSMTP(); // Define que a mensagem ser� SMTP
        $mail->Host = $configs['host']; // Seu endere�o de host SMTP
        $mail->SMTPAuth = true; // Define que ser� utilizada a autentica��o -  Mantenha o valor "true"
        $mail->Port = $configs['port']; // Porta de comunica��o SMTP - Mantenha o valor "587"
        $mail->SMTPSecure = false; // Define se � utilizado SSL/TLS - Mantenha o valor "false"
        $mail->SMTPAutoTLS = false; // Define se, por padr�o, ser� utilizado TLS - Mantenha o valor "false"
        $mail->Username = $configs['username']; // Conta de email existente e ativa em seu dom�nio
        $mail->Password = $configs['password']; // Senha da sua conta de email
        // DADOS DO REMETENTE
        $mail->Sender = $configs['sender']; // Conta de email existente e ativa em seu dom�nio
        $mail->From = $configs['sender']; // Sua conta de email que ser� remetente da mensagem
        $mail->FromName = I18n::get('layout.topBarSiteDescription'); // Nome da conta de email
        // DADOS DO DESTINAT�RIO
        $mail->AddAddress($submitter->email->unwrapOr('n@a'), $submitter->full_name->unwrapOr('autor(a)')); // Define qual conta de email receber� a mensagem

        // Defini��o de HTML/codifica��o
        $mail->IsHTML(true); // Define que o e-mail ser� enviado como HTML
        $mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)
        // DEFINI��O DA MENSAGEM
        $mail->Subject  = "ABEL - Seu artigo foi aprovado!"; // Assunto da mensagem

        ob_start();
        $__VIEW = 'article-approved-notification-message.php';
        require_once (__DIR__ . '/../../Mail/email-base-body.php');
        $emailBody = ob_get_clean();
        ob_end_clean();

        $mail->Body .= $emailBody;
        
        $sent = $mail->Send();

        $mail->ClearAllRecipients();

        // Exibe uma mensagem de resultado do envio (sucesso/erro)
        //if (!$sent)
         //   throw new \Exception("Não foi possível enviar o e-mail! Detalhes do erro: " . $mail->ErrorInfo);
    } 
}