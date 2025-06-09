<?php
session_start();
include '../Model/Professor.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $rg    = $_POST['rg'];

    // Montar array de dias e aulas
    $diasAulas = [];
    if (!empty($_POST['dias'])) {
        foreach ($_POST['dias'] as $dia) {
            $campo = 'aulas_' . $dia;
            if (isset($_POST[$campo]) && (int)$_POST[$campo] > 0) {
                $diasAulas[$dia] = (int)$_POST[$campo];
            }
        }
    }

    $professor = new Professor();
    $resultado = $professor->cadastrar($nome, $email, $senha, $rg, $diasAulas);

    if ($resultado) {
        $_SESSION['cadastro_sucesso'] = "Usuário cadastrado com sucesso!";
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['cadastro_erro'] = "Erro ao cadastrar usuário! Verifique os dados ou tente novamente.";
        header("Location: ../index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - SISESCOLAR</title>
    <link href="estilo.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="text-center">Cadastro de Professor</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome do Docente</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email"name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">RG</label>
                                <input type="text" class="form-control" id="rg"name="rg" required>
                            </div>
                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>



                            <h5 class="mt-3">Carga Horária Semanal</h5>

                    <?php
                    $diasSemana = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
                    foreach ($diasSemana as $dia):
                        $label = ucfirst($dia);
                    ?>
                    <div class="dia-semana mb-2">
                        <label>
                            <input type="checkbox" name="dias[]" value="<?= $dia ?>" onchange="document.getElementById('aulas_<?= $dia ?>').disabled = !this.checked;">
                            <?= $label ?>
                        </label>
                        <input type="number" class="form-control d-inline w-auto ms-2" id="aulas_<?= $dia ?>" name="aulas_<?= $dia ?>" min="0" max="8" value="0" disabled>
                    </div>
                    <?php endforeach; ?>


                             <!-- Tipo de usuário fixo: professor (campo oculto) -->
                            <button type="submit" class="btn btn-primary w100">Cadastrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>