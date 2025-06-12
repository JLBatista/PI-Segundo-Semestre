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
    <style>

      /* Garante que todos os cards tenham o mesmo tamanho */
.linha-admin-cards {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: center;
    gap: 32px; /* Espaço de 32px entre os cards */
    margin-top: 40px;
}

.menu-box {
    width: 300px; /* Tamanho fixo para todos os cards */
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgb(0 0 0 / 0.13);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 28px 18px 18px 18px;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
}
    </style>
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
        
        <div class="linha-admin-cards">
    <div class="menu-box" onclick="window.location.href='cadastro_professor.php'">
        <img src="../assets/icon-doc-gray.png" alt="Analisar" class="menu-icon">
        <h2>PROFESSOR</h2>
        <p>Criar Login de Professor</p>
    </div>
    <div class="menu-box" onclick="window.location.href='cadastro_diretor.php'">
        <img src="../assets/icon-doc-gray.png" alt="Analisar" class="menu-icon">
        <h2>DIREÇÃO</h2>
        <p>Criar Login de Diretor</p>
    </div>
    <div class="menu-box" onclick="window.location.href='cadastro_secretaria.php'">
        <img src="../assets/icon-doc-gray.png" alt="Analisar" class="menu-icon">
        <h2>SECRETARIA</h2>
        <p>Criar Login de Secretaria</p>
    </div>
    <div class="menu-box" onclick="window.location.href='controle_cadastro.php'">
        <img src="../assets/icon-doc-gray.png" alt="Analisar" class="menu-icon">
        <h2>USUÁRIOS</h2>
        <p>Verifica os usuários cadastrados no banco do sistema</p>
    </div>
</div>

        
    </main>

    <!-- Rodapé -->
    <footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html> 