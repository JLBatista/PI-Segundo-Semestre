<?php 
session_start();

include '../model/Professor.php';
$professor = new Professor();

if (!isset($_SESSION['id'])) {

    header("Location: ../index.php");
    exit;
}


$nome = $_SESSION['nome'];

?>
</html><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Links do cabeçalho da Fatec -->
    <link rel="stylesheet" type="text/css" href="https://fatecitapira.cps.sp.gov.br/wp-content/themes/tema-cps/css/sao-paulo/topo-padrao-govsp.min.css">
    <link rel="stylesheet" type="text/css" href="https://fatecitapira.cps.sp.gov.br/wp-content/themes/tema-cps/css/sao-paulo/barra-contraste-govsp.min.css">
    <link rel="stylesheet" type="text/css" href="https://fatecitapira.cps.sp.gov.br/wp-content/themes/tema-cps/css/sao-paulo/rodape-padrao-govsp.min.css">
    <script src="https://kit.fontawesome.com/fa068c530f.js" crossorigin="anonymous"></script>
    <title>Dashboard HAE</title>
    <link rel="stylesheet" href="estilo.css">
    
</head>
<body>
   <!-- Cabeçalho -->
    <div class="header">
        <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
        <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
    </div>

    <hr>

    <!-- Menu de navegação -->
    <nav class="nav-menu">
        <a href="../index.php" class="nav-item">Home</a>
        <a href="../index.php" class="nav-item">Voltar</a>
        <a href="../controller/logout.php" class="nav-item">Sair</a>
    </nav>

    <!-- Área principal -->
    <main class="main">
        <h1>Menu Professores</h1>
        
        <div class="menu-container">
            <div class="menu-box" onclick="window.location.href='formulario.php'">
                <img src="../assets/icon-doc-gray.png" alt="Inscrição" class="menu-icon">
                <h2>INSCRIÇÃO</h2>
                <p>Realize sua inscrição para atividades</p>
            </div>

            <div class="menu-box" onclick="window.location.href='acompanhar.php'">
                <img src="../assets/icon-com-gray.png" alt="Acompanhar" class="menu-icon">
                <h2>ACOMPANHAR</h2>
                <p>Acompanhe suas inscrições e status</p>
            </div>
        </div>

    </main>


    <!-- TOAST (popup) Bootstrap -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastSucesso" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Solicitação enviada com sucesso!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
        </div>
    </div>
</div>

<!-- JS do Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Verifica se veio o parâmetro sucesso na URL
    const urlParams = new URLSearchParams(window.location.search);
    const sucesso = urlParams.get('sucesso');

    if (sucesso === '1') {
        const toastElement = document.getElementById('toastSucesso');
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
</script>

    <!-- Rodapé -->
    <footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html>