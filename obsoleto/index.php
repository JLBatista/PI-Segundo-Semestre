<?php
session_start();

// Verifica se a sessão existe e o tipo_usuario está definido
if (isset($_SESSION['id']) && isset($_SESSION['tipo_usuario'])) {
    if ($_SESSION['tipo_usuario'] == 'administrador') {
        header("Location: view/admin.php");
        exit;
    } elseif ($_SESSION['tipo_usuario'] == 'professor') {
        header("Location: view/dashboard_professor.php");
        exit;
    } elseif ($_SESSION['tipo_usuario'] == 'coordenacao') {
        header("Location: view/coordenacao.php");
        exit;
    } elseif ($_SESSION['tipo_usuario'] == 'direcao') {
        header("Location: view/direcao.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - SISESCOLAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-center">Login</h4>
                </div>
                <div class="card-body">
                    <?php
                    if (isset($_SESSION['login_error'])) {
                        echo "<div class='alert alert-danger'>" . $_SESSION['login_error'] . "</div>";
                        unset($_SESSION['login_error']);
                    }
                    ?>
                    <form method="POST" action="controller/login.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required />
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" required />
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
