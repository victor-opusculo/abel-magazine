<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Submitters;

use DateInterval;
use DateTime;
use DateTimeZone;
use mysqli;
use PHPMailer\PHPMailer\PHPMailer;
use VictorOpusculo\AbelMagazine\Lib\Helpers\Data;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Option;

/**
 * @property Option<int> id 
 * @property Option<int> submitter_id 
 * @property Option<string> otp 
 * @property Option<string> expiry_datetime 
 */
class SubmitterOtp extends DataEntity
{
    public function __construct(?array $initialValues = null)
    {
        $this->properties = (object)
        [
            'id' => new DataProperty(null, fn() => null, DataProperty::MYSQL_INT),
            'submitter_id' => new DataProperty(null, fn() => null, DataProperty::MYSQL_INT),
            'otp' => new DataProperty(null, fn() => null, DataProperty::MYSQL_STRING),
            'expiry_datetime' => new DataProperty(null, 
                fn() => (new DateTime('now', new DateTimeZone('UTC')))->add(new DateInterval('PT15M'))->format('Y-m-d H:i:s'), 
                DataProperty::MYSQL_STRING
            )
        ];

        parent::__construct($initialValues);
    }

    protected string $databaseTable = 'submitter_otps';
    protected string $formFieldPrefixName = 'submitter_otps';
    protected array $primaryKeys = ['id'];

    public function verifyOtp(string $givenOtp) : bool
    {
        $hashed = $this->properties->otp->getValue()->unwrapOr('');
        return password_verify($givenOtp, $hashed);
    }

    public function verifyDatetime() : bool
    {
        $dt = $this->properties->expiry_datetime->getValue()->unwrapOr('2000-01-01 12:00:00');
        $dt = new DateTime($dt, new DateTimeZone('UTC'));
        $now = new DateTime('now', new DateTimeZone('UTC'));

        return $now <= $dt;
    }

    public function clearAllOtpsFromSubmitter(mysqli $conn) : void
    {
        $subId = $this->submitter_id->unwrapOr(0);
        $stmt = $conn->prepare("DELETE FROM submitter_otps WHERE submitter_id = ?");
        $stmt->bind_param('i', $subId);
        $stmt->execute();
        $stmt->close();
    }

    /** @return array{SubmitterOtp, string} */
    public static function createNow(int $submitterId) : array
    {
        $otp = mt_rand(100000, 999999);
        $new = new self([ 'submitter_id' => $submitterId, 'otp' => password_hash($otp, PASSWORD_DEFAULT) ]);
        $new->properties->expiry_datetime->resetValue();
        return [ $new, $otp ];
    }

    public static function sendEmail(string $otp, string $submitterEmail, string $submitterName) : void
    {
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
        $mail->AddAddress($submitterEmail, $submitterName); // Define qual conta de email receber� a mensagem

        // Defini��o de HTML/codifica��o
        $mail->IsHTML(true); // Define que o e-mail ser� enviado como HTML
        $mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)
        // DEFINI��O DA MENSAGEM
        $mail->Subject  = I18n::get('mail.recoverLoginTitle'); // Assunto da mensagem

        ob_start();
        $oneTimePassword = $otp;
        $__VIEW = 'submitter-recover-password-otp-message.php';
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