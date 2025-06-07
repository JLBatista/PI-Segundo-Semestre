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

    <!-- Rodapé -->
    <footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html>