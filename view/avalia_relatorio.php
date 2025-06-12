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
$relatorios = $diretor->listarRelatoriosHAE();

function iconeStatus($status) {
    $map = [
        'Deferido' => 'check_circle',
        'Deferido Parcialmente' => 'check_circle_outline',
        'Indeferido' => 'cancel',
        'Aguardando' => 'hourglass_empty',
        'aprovado' => 'check_circle',
        'reprovado' => 'cancel',
        'pendente' => 'hourglass_empty'
    ];
    return $map[$status] ?? '';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Avaliação de Relatórios HAE - Hora+</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="estilo.css">
    <style>
        .cabeca {
            text-align: center;
            margin-bottom: 40px;
            background:#c8102e;
            padding: 30px;
            border-radius: 8px;
            color: white;
        }
        .containeres {
            max-width: 1200px;
            margin: 40px auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .relatorio-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            justify-content: center;
        }
        .relatorio-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.13);
            width: 370px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 370px;
            margin-bottom: 20px;
            padding: 0;
        }
        .relatorio-info {
            padding: 24px 24px 18px 24px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .professor-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
            text-align: center;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            text-align: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            margin: 12px 0 10px 0;
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
        .status-badge.aguardando, .status-badge.pendente {
            background-color: #9e9e9e;
        }
        .status-badge.aprovado {
            background-color: #198754;
        }
        .status-badge.reprovado {
            background-color: #cc1719;
        }
        .tipo-hae, .curso-hae {
            margin-top: 6px;
            font-size: 16px;
            color: #333;
            text-align: center;
        }
        .resultados {
            margin-top: 12px;
            font-size: 15px;
            color: #444;
            background: #f8f9fa;
            border-radius: 6px;
            padding: 10px;
            width: 100%;
            text-align: center;
        }
        .arquivo-link {
            margin-top: 14px;
            display: inline-block;
            color: #0d6efd;
            font-weight: bold;
            text-align: center;
        }
        .parecer-form {
            margin-top: 18px;
            width: 100%;
            text-align: center;
        }
        .parecer-label {
            font-weight: bold;
            margin-right: 10px;
        }
        .btn-aprovar, .btn-reprovar {
            border: none;
            border-radius: 6px;
            padding: 7px 18px;
            font-weight: bold;
            margin: 0 5px;
            color: #fff;
            cursor: pointer;
            font-size: 15px;
        }
        .btn-aprovar {
            background: #198754;
        }
        .btn-aprovar:hover {
            background: #146c43;
        }
        .btn-reprovar {
            background: #cc1719;
        }
        .btn-reprovar:hover {
            background: #a40d24;
        }
        .parecer-atual {
            margin-top: 18px;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            text-align: center;
        }
        .aviso-status {
            margin-top: 10px;
            color: #b02a37;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
        <h1 class="alinhamento">Avaliação de Relatórios HAE</h1>
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
            <h1>Relatórios enviados</h1>
            <p class="subtitle">Veja abaixo os relatórios submetidos pelos professores</p>
        </div>
        <div class="relatorio-grid">
            <?php foreach ($relatorios as $r):
                $statusClasse = strtolower(str_replace(' ', '-', $r['status_final']));
                $curso = strtoupper(trim($r['curso']));
                $tipo = htmlspecialchars($r['tipo']);
                $nome = htmlspecialchars($r['nome_professor']);
                $status = htmlspecialchars($r['status_final']);
                $resultados = htmlspecialchars($r['resultados']);
                $arquivo = htmlspecialchars($r['arquivo_upload']);
            ?>
            <div class="relatorio-card" data-curso="<?= $curso ?>" data-status="<?= $statusClasse ?>">
                <div class="relatorio-info">
                    <div class="professor-name"><?= $nome ?></div>
                    <div class="curso-hae"><strong>Curso:</strong> <?= $curso ?></div>
                    <div class="tipo-hae"><strong>Tipo:</strong> <?= $tipo ?></div>
                    <span class="status-badge <?= $statusClasse ?>">
                        <span class="material-icons" style="font-size:16px; vertical-align: middle;"><?= iconeStatus($status) ?></span>
                        <?= $status ?>
                    </span>
                    <div class="resultados"><strong>Resultados:</strong> <?= nl2br($resultados) ?></div>
                    <?php if ($arquivo): ?>
                        <a class="arquivo-link" href="../uploads_relatorio/<?= $arquivo ?>" target="_blank">
                            📎 Ver arquivo enviado
                        </a>
                    <?php endif; ?>

                    <?php if ($status === 'aprovado' || $status === 'reprovado'): ?>
                        <div class="parecer-atual">
                            Parecer:
                            <?php if ($status == 'aprovado'): ?>
                                <span style="color:#198754;">Aprovado</span>
                            <?php else: ?>
                                <span style="color:#cc1719;">Reprovado</span>
                            <?php endif; ?>
                            <div class="aviso-status">
                                Status deferido. Não é mais possível editar este relatório.
                            </div>
                        </div>
                    <?php else: ?>
                        <form class="parecer-form" method="POST" action="../controller/parecer_relatorio.php">
                            <input type="hidden" name="relatorio_id" value="<?= $r['id'] ?>">
                            <span class="parecer-label">Parecer:</span>
                            <button type="submit" name="parecer" value="aprovado" class="btn-aprovar">Aprovar</button>
                            <button type="submit" name="parecer" value="reprovado" class="btn-reprovar">Reprovar</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html>