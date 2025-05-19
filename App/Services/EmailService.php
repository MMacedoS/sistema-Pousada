<?php
namespace App\Services;

use App\Utils\LoggerHelper;
use Exception;
use PHPMailer;

class EmailService
{
    private $config;

    public function __construct()
    {
        $this->config = [
            'host' => 'smtp.gmail.com', // Servidor SMTP
            'port' => 587, // Porta do servidor
            'username' => 'geeducsoftware@gmail.com', // E-mail do remetente
            'password' => 'bfik kbbt oukv bfaz', // Senha do e-mail
            'encryption' => 'tls', // Tipo de criptografia (tls/ssl)
            'from_email' => 'contato@unooba.org.br', // E-mail do remetente
            'from_name' => 'Gestor Unooba' // Nome do remetente
        ];
    }

    public function sendEmail(string $to, string $subject, string $message): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = $this->config['encryption'];
            $mail->Port = $this->config['port'];

            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            return true;
        } catch (Exception $e) {
            LoggerHelper::logInfo("Erro ao enviar e-mail: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function prepareMessageRecoveryPassword($user, $recovery) 
    {
        $message = '<h1>Olá! ' . $user->nome . ' </h1><p>Esta é o retorno de sua solicitação de recuperação de senha, por favor acesse o </p>';
        $message .= '</br> <a href="'.$_ENV['URL_PREFIX_APP'].'/recuperar/'. $recovery .'">link de recuperação</a>';
        $message .= '</br></br>
        <p>Para mais informações entre en contato!!</p>
        </br></br>Att: Unooba - ' . $_ENV['URL_PREFIX_APP'];

        return $message;
    }
}