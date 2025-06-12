<?php
session_start();
include '../model/Secretaria.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $secretaria = new Secretaria();
    $resultado = $secretaria->cadastrar($nome, $email, $senha);

    if ($resultado) {
        $_SESSION['cadastro_sucesso'] = "Secretaria cadastrada com sucesso!";
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['cadastro_erro'] = "Erro ao cadastrar secretaria! Verifique os dados ou tente novamente.";
        header("Location: ../index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Secretaria - SISESCOLAR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilo.css">
    <style>
        :root {
            --fatec-azul: #003e7e;
            --fatec-vermelho: #cc1719;
            --fatec-cinza: #666666;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:rgb(232, 228, 228);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .main {
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px;
        }
        .header h1 {
            margin: 0 auto;
            text-align: center;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        .container {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        h1, h4 {
            color: var(--fatec-vermelho);
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            font-weight: 600;
            color: var(--fatec-cinza);
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #ccc;
        }
        .form-control:focus {
            border-color: var(--fatec-vermelho);
            box-shadow: 0 0 5px rgba(204, 23, 25, 0.5);
        }
        .btn-primary {
            background-color: var(--fatec-vermelho);
            border: none;
            padding: 10px 25px;
            font-weight: bold;
            border-radius: 10px;
            margin-top: 18px;
        }
        .btn-primary:hover {
            background-color: #a40d24;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
        <h1>Cadastro de Secretaria</h1>
        <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
    </div>
    <hr>
    <!-- Menu de navegação -->
    <nav class="nav-menu">
        <a href="../index.php" class="nav-item">Home</a>
        <a href="../index.php" class="nav-item">Voltar</a>
        <a href="../controller/logout.php" class="nav-item">Sair</a>
    </nav>
    <div class="container">
        <h4>Preencha os dados para cadastro</h4>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome da Secretaria</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
        </form>
    </div>
    <footer class="footer mt-4">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html>