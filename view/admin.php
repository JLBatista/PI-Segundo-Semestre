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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column align-items-center justify-content-center min-vh100 bg-light">
    <div class="container text-center p-5 shadow-sm rounded bg-white">
        <h1 class="mb-4">Olá, Administrador 
            <?php
                echo htmlspecialchars($nome);
            ?>!<h1>
                <div class="mb-4">
                    <a href="cadastro_professor.php" class="btn btn-primary btn-lg">Cadastrar Professor</a>
                </div>
            <a href="../controller/logout.php" class="btn btn-danger btn-lg">Sair</
        </div>  
    </body>
</html>