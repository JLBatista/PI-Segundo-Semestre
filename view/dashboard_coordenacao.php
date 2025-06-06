<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

// Verifica se o usuário é da direção
if ($_SESSION['tipo_usuario'] !== 'direcao') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
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
        <h1>Menu Direção</h1>
        
        <div class="menu-container">
            <div class="menu-box" onclick="window.location.href='avalia.php'">
                <img src="../assets/icon-doc-gray.png" alt="Analisar" class="menu-icon">
                <h2>ANALISAR</h2>
                <p>Analise as inscrições e relatórios</p>
            </div>

            <div class="menu-box" onclick="window.location.href='relatorio.php'">
                <img src="../assets/icon-com-gray.png" alt="Relatório" class="menu-icon">
                <h2>RELATÓRIO</h2>
                <p>Visualize os relatórios do sistema</p>
            </div>
        </div>

        
    </main>

    <!-- Rodapé -->
    <footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html> 