<?php
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['tipo_usuario'])) {
    switch ($_SESSION['tipo_usuario']) {
        case 'administrador':
            header("Location: view/admin.php");
            exit;
        case 'professor':
            header("Location: view/dashboard_professor.php");
            exit;
        case 'diretor':
            header("Location: view/dashboard_coordenacao.php");
            exit;
        case 'direcao':
            header("Location: view/dashboard_coordenacao.php");
            exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tela de Login</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <!-- Cabeçalho -->
    <div class="header">
        <img src="assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
        <img src="assets/hora.png" alt="Logo Hora+" style="height: 50px;">
    </div>

    <hr>

    <!-- Conteúdo principal -->
    <div class="main">

        <!-- Banner -->
       <div class="banner">
    <img src="assets/fundo.png" alt="Banner de Fundo" class="banner-bg">
    <img src="assets/alunos.png" alt="Imagem de Alunos" class="alunos-img">
</div>

        <!-- Caixa de login -->
        <div class="login-box">
            <h1>Bem-vindo ao <span style="color: #c8102e; font-weight: bold;">Hora +</span></h1><br>
            <h3 style="color: black">Sistema de Hora Atividade Específica</h3><br><br>

            <h2>Login</h2>

            <?php
            if (isset($_SESSION['login_error'])) {
                echo "<div class='alert alert-danger'>" . $_SESSION['login_error'] . "</div>";
                unset($_SESSION['login_error']);
            }
            ?>

            <form method="POST" action="controller/login.php">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required />
                </div>

                <div class="form-group">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required />
                </div>

                <div class="form-group">
                    <a href="#">Esqueceu a senha?</a>
                </div>

                <button type="submit">Entrar</button>
            </form>
        </div>
    </div>

    <!-- Rodapé -->
    <footer class="footer">
        <p>© 2025 Fatec Itapira - Todos os direitos reservados</p>
    </footer>

</body>
</html>
