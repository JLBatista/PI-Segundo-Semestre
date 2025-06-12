<?php
session_start();
if (!isset($_SESSION['id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'secretaria') {
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
    <title>Dashboard Secretaria - Hora+</title>
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   
</head>
<!-- Cabeçalho -->
    <div class="header">
        <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
        <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
    </div>

    <!-- Menu de navegação -->
    <nav class="nav-menu">
        <a href="../index.php" class="nav-item">Home</a>
        <a href="../index.php" class="nav-item">Voltar</a>
        <a href="../controller/logout.php" class="nav-item">Sair</a>
    </nav>

    <!-- Área principal -->
    <main class="main">
        <h1>Menu Secretaria</h1>
        
        <div class="menu-container">
            <div class="menu-box" onclick="window.location.href='publicar_edital.php'">
                <img src="../assets/icon-doc-gray.png" alt="Publicar" class="menu-icon">
                <h2>PUBLICAR EDITAL</h2>
                <p>Publique um novo edital no sistema</p>
            </div>

            <div class="menu-box" onclick="window.location.href='ver_editais.php'">
                <img src="../assets/icon-com-gray.png" alt="Ver Editais" class="menu-icon">
                <h2>VER EDITAL</h2>
                <p>Visualize os editais já postados no sistema</p>
            </div>
        </div>

        
    </main>
    <footer class="footer mt-4">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html>