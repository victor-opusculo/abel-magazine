<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Settings;

use PHPMailer\PHPMailer\PHPMailer;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Option;

/**
 * @property Option<string> name
 * @property Option<string> value
 */
final class EmailToContact extends DataEntity
{
    public function __construct($initialValues = null)
    {
        $this->properties = (object)
        [
            'name' => new DataProperty(null, fn() => 'EMAIL_CONTACT', DataProperty::MYSQL_STRING),
            'value' => new DataProperty(null, fn() => null, DataProperty::MYSQL_STRING)
        ];

        parent::__construct($initialValues);

        $this->properties->name->setValue(Option::some('EMAIL_CONTACT'));
    }

    protected string $databaseTable = 'settings';
    protected string $formFieldPrefixName = 'emailContact';
    protected array $primaryKeys = ['name'];
    protected array $setPrimaryKeysValue = ['name'];

    public function sendEmail(string $senderName, string $senderEmail, string $senderTelephone, string $senderSubject, string $senderMessage) : void
    {
        if (!$this->value->unwrapOr(null))
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
        $mail->addReplyTo($senderEmail, $senderName);
        $mail->From = $configs['sender']; // Sua conta de email que ser� remetente da mensagem
        $mail->FromName = I18n::get('layout.topBarSiteDescription'); // Nome da conta de email
        // DADOS DO DESTINAT�RIO
        $mail->AddAddress($this->value->unwrapOr('n@a'), "ABEL"); // Define qual conta de email receber� a mensagem

        // Defini��o de HTML/codifica��o
        $mail->IsHTML(true); // Define que o e-mail ser� enviado como HTML
        $mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)
        // DEFINI��O DA MENSAGEM
        $mail->Subject  = "Revista - " . $senderSubject; // Assunto da mensagem

        ob_start();
        $__VIEW = 'contact-form-message.php';
        require_once (__DIR__ . '/../../Mail/email-base-body.php');
        $emailBody = ob_get_clean();
        ob_end_clean();

        $mail->Body .= $emailBody;
        
        $sent = $mail->Send();

        $mail->ClearAllRecipients();

        // Exibe uma mensagem de resultado do envio (sucesso/erro)
        if (!$sent)
            throw new \Exception("Não foi possível enviar o e-mail! Detalhes do erro: " . $mail->ErrorInfo);
    } 
}