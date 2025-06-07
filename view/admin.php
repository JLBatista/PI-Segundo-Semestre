<?php

session_start();

if (!isset($_SESSION['id'])) {

    header("Location: ../index.php");
    exit;
}

$nome = $_SESSION['nome'];

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Direção - Hora+</title>
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
        <h1>Menu Administrador</h1>
        
        <div class="menu-container">
            <div class="menu-box" onclick="window.location.href='cadastro_professor.php'">
                <img src="../assets/icon-doc-gray.png" alt="Analisar" class="menu-icon">
                <h2>PROFESSOR</h2>
                <p>Criar Login de Professor</p>
            </div>

            <div class="menu-box" onclick="window.location.href='cadastro_coordenador.php'">
                <img src="../assets/icon-doc-gray.png" alt="Analisar" class="menu-icon"> <!-- Necessario alterar as imagens -->
                <h2>COORDENAÇÃO</h2>
                <p>Criar Login de Coordenador</p>
            </div>

            <div class="menu-box" onclick="window.location.href='cadastro_secretaria.php'">
                <img src="../assets/icon-doc-gray.png" alt="Analisar" class="menu-icon">
        <h2>SECRETARIA</h2>
        <p>Criar Login de Secretaria</p>
    </div>
        </div>

        
    </main>

    <!-- Rodapé -->
    <footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html> 