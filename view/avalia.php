<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SESSION['tipo_usuario'] !== 'direcao') {
    header("Location: ../index.php");
    exit;
}

require_once '../model/Diretor.php';

$diretor = new Diretor();
$solicitacoes = $diretor->listarSolicitacoesHAE();

function iconeStatus($status) {
    $map = [
        'Deferido' => 'check_circle',
        'Deferido Parcialmente' => 'check_circle_outline',
        'Indeferido' => 'cancel',
        'Aguardando' => 'hourglass_empty'
    ];
    return $map[$status] ?? '';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Menu Direção - Hora+</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="estilo.css">
    
    <style>
        .cabeca {
            text-align: center;
            margin-bottom: 40px;
            background:rgb(126, 0, 0);
            padding: 30px;
            border-radius: 8px;
            color: white;
        }

        .alinhamento{
            text-align: center;
        }

        .containeres {
            max-width: 1200px;
            margin: 40px auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .professor-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .professor-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
            width: 250px;
            cursor: pointer;
            transition: box-shadow 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .professor-card:hover {
            box-shadow: 0 4px 12px rgb(0 0 0 / 0.2);
        }

        .avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color:rgb(32, 32, 36); /* azul anil fixo */
            border-radius: 8px 8px 0 0;
        }

        .avatar-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .professor-info {
            padding: 15px;
        }

        .departamento-tag {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .professor-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            text-align: center;
            gap: 5px;
            padding: 5px 8px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }

        .status-badge.deferido {
            background-color: #4caf50;
        }

        .status-badge.deferido-parcialmente {
            background-color: #ff9800;
        }

        .status-badge.indeferido {
            background-color: #f44336;
        }

        .status-badge.aguardando {
            background-color: #9e9e9e;
        }

        .tipo-hae {
            margin-top: 12px;
            font-size: 16px;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

    </style>
</head>
<body>
    <div class="header">
        <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
        <h1 class="alinhamento">Portal da Coordenação</h1>
        <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
    </div>

    <hr>

    <nav class="nav-menu">
        <a href="../index.php" class="nav-item">Home</a>
        <a href="../index.php" class="nav-item">Voltar</a>
        <a href="../controller/logout.php" class="nav-item">Sair</a>
    </nav>

    <div class="containeres">
        <div class="cabeca">
            <h1>Avaliação de HAE</h1>
            <p class="subtitle">Selecione um professor para avaliar seus projetos HAE</p>
        </div>

        <div class="professor-grid">
            <?php foreach ($solicitacoes as $s):
                $statusClasse = strtolower(str_replace(' ', '-', $s['status']));
                $curso = strtoupper(trim($s['curso']));
                $tipo = htmlspecialchars($s['tipo']);
                $nome = htmlspecialchars($s['nome']);
                $status = htmlspecialchars($s['status']);
                $solicitacaoId = $s['id'];

                $img = match($curso) {
                    'DSM' => '../assets/dsm.png',
                    'GE'  => 'ge.png',
                    'GPI' => '../assets/gpi.png',
                    default => 'default.png',
                };
            ?>
            <a href="resumo-projeto.php?solicitacao=<?= $solicitacaoId ?>" class="professor-card" data-curso="<?= $curso ?>" data-status="<?= $statusClasse ?>">
                <div class="avatar">
                    <img src="../assets/<?= $img ?>" alt="<?= $curso ?>" class="avatar-img">
                </div>
                <div class="professor-info">
                    <div class="departamento-tag">
                        <span style="font-size:18px; vertical-align: middle;">Curso:</span>
                        <?= $curso ?>
                    </div>
                    <div class="professor-name"><?= $nome ?></div>
                    <span class="status-badge <?= $statusClasse ?>">
                        <span class="material-icons" style="font-size:16px; vertical-align: middle;"><?= iconeStatus($status) ?></span>
                        <?= $status ?>
                    </span>
                    <div class="tipo-hae"><strong>Tipo:</strong> <?= $tipo ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html>
