<?php
// Inclui a classe Diretor e o arquivo de envio de e-mails
require_once 'Diretor.php';
require_once 'mailer.php';

// Cria uma instância da classe Diretor para acessar o banco e métodos
$diretor = new Diretor();

// Recebe os dados enviados via POST (id da solicitação e novo status)
$solicitacaoId = $_POST['id'] ?? null;
$novoStatus = $_POST['status'] ?? null;

// Verifica se recebeu os dados obrigatórios
if ($solicitacaoId && $novoStatus) {
    // Busca os dados da solicitação junto com dados do professor
    $dados = $diretor->buscarSolicitacaoComProfessor($solicitacaoId);

    if ($dados) {
        // Pega os dados importantes para o e-mail
        $nome = $dados['nome'];
        $email = $dados['email'];
        $tipo = $dados['tipo'];

        // Chama a função do mailer para enviar o e-mail
        // Essa função precisa estar definida em mailer.php
        enviarEmailStatusHAE($email, $nome, $novoStatus, $tipo);

        echo "E-mail enviado para $nome ($email) com status $novoStatus.";
    } else {
        // Caso não encontre a solicitação no banco
        echo "Solicitação não encontrada.";
    }
} else {
    // Caso algum dado obrigatório não tenha sido enviado no POST
    echo "Dados insuficientes para envio do e-mail.";
}
