<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // ajuste o caminho se estiver diferente

function enviarEmailStatusHAE($para, $nomeProfessor, $status, $tipoHAE) {
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'horamaisfatec@gmail.com'; // seu email
        $mail->Password   = 'antrmqvdmjlrcxml'; // senha de app do Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Remetente e destinatário
        $mail->setFrom('horamaisfatec@gmail.com', 'Hora+ Fatec Itapira');
        $mail->addAddress($para, $nomeProfessor);

        // Conteúdo
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';  //  Adicionado para suportar caracteres especiais
        $mail->Subject = 'Resultado da sua solicitação de HAE';  // Corrigido com acento
        $mail->Body    = "
            <h2>Olá, $nomeProfessor</h2>
            <p>O status da sua solicitação de HAE foi atualizado.</p>
            <p><strong>Tipo:</strong> $tipoHAE</p>
            <p><strong>Status:</strong> $status</p>
            <br>
            <p>Att,<br>Coordenação Hora+ - Fatec Itapira</p>
    ";


        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        return false;
    }
}
