<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: ../index.php");
    exit;
}

require_once '../model/Professor.php';

$professor = new Professor();
$idProfessor = $professor->getIdProfessorPorUsuarioId($_SESSION['id']);
$solicitacoes = $professor->listarSolicitacoesDoProfessor($idProfessor);

function iconeStatus($status) {
    $map = [
        'Deferido' => 'check_circle',
        'Deferido Parcialmente' => 'check_circle_outline',
        'Indeferido' => 'cancel',
        'Aguardando' => 'hourglass_empty'
    ];
    return $map[$status] ?? '';
}

// Buscar IDs das HAE já relatadas
$pdo = $professor->getPdo();
$stmtRel = $pdo->prepare("SELECT solicitacao_hae_id FROM hae_deferidas WHERE professor_id = :prof");
$stmtRel->execute([':prof' => $idProfessor]);
$haeComRelatorio = $stmtRel->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <meta charset="UTF-8">
    <title>Acompanhar HAE - Hora+</title>
    <link rel="stylesheet" href="estilo.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .cabeca {
            text-align: center;
            margin-bottom: 40px;
            background:rgb(126, 0, 0);
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

        .solicitacao-card {
            border: 1px solid #ddd;
            border-left: 8px solid #9e9e9e;
            padding: 48px 36px;
            margin-bottom: 0;
            border-radius: 8px;
            transition: border-color 0.3s ease;
            cursor: pointer;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            min-height: 220px;
        }

        .solicitacao-card.deferido {
            border-left-color: #4caf50;
        }

        .solicitacao-card.deferido-parcialmente {
            border-left-color: #ff9800;
        }

        .solicitacao-card.indeferido {
            border-left-color: #f44336;
        }

        .solicitacao-card.aguardando {
            border-left-color: #9e9e9e;
        }

        .solicitacao-card.relatorio-enviado .badge {
    font-size: 1em;
    position: static;
    display: block;
    margin: 10px auto 10px auto; /* 16px de espaço abaixo do badge */
    text-align: center;
}
        .solicitacao-card .btn-editar-relatorio {
            background: #cc1719;
            color: #fff;
            border: none;
            font-weight: bold;
            border-radius: 8px;
            padding: 8px 22px;
            opacity: 1 !important;
            cursor: pointer !important;
            z-index: 2;
            position: relative;
            margin-top: 10px;
        }
        .solicitacao-card .btn-editar-relatorio:hover {
            background: #a40d24;
        }

        .status {
            display: inline-flex;
            align-items: center;
            font-weight: bold;
            gap: 8px;
            font-size: 15px;
        }

        .tipo {
            font-size: 16px;
            margin-top: 10px;
        }
        .professor-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 48px;
        }
        .alert-success {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
    <h1 style="text-align:center;">Acompanhar Solicitações HAE</h1>
    <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
</div>

<nav class="nav-menu">
    <a href="../index.php" class="nav-item">Home</a>
    <a href="dashboard_professor.php" class="nav-item">Voltar</a>
    <a href="../controller/logout.php" class="nav-item">Sair</a>
</nav>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'relatorio_salvo'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="max-width:900px;margin:30px auto 0;display:flex;justify-content:center;align-items:center;">
        <span style="flex:1;text-align:center;">Relatório enviado com sucesso!</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<div class="containeres">
    <div class="cabeca">
        <h1>Realizar relatório</h1>
        <p class="subtitle">Veja abaixo suas solicitações deferidas para gerar relatório</p>
    </div>
    <div class="professor-grid">
        <?php if (empty($solicitacoes)): ?>
            <p style="text-align:center;">Nenhuma solicitação registrada ainda.</p>
        <?php else: ?>
            <?php foreach ($solicitacoes as $s):
                if (strtolower($s['status']) !== 'deferido') continue;
                $statusClasse = strtolower(str_replace(' ', '-', $s['status']));
                $relatorioEnviado = in_array($s['id'], $haeComRelatorio);
            ?>
                <div 
    class="solicitacao-card <?= $statusClasse ?> <?= $relatorioEnviado ? 'relatorio-enviado' : '' ?>" 
    <?php if (!$relatorioEnviado): ?>
        onclick="window.location.href='form_relatorio.php?id=<?= $s['id'] ?>'"
        title="Clique para gerar relatório"
    <?php else: ?>
        title="Relatório já enviado"
    <?php endif; ?>
>
    <?php if ($relatorioEnviado): ?>
        <span class="badge bg-success">Relatório enviado</span>
    <?php endif; ?>
    <div class="tipo">
        <strong>Tipo:</strong> <?= htmlspecialchars($s['tipo']) ?>
    </div>
    <div class="tipo">
        <strong>Curso:</strong> <?= htmlspecialchars($s['curso']) ?>
    </div>
    <div class="tipo">
        <strong>ID Solicitação:</strong> <?= $s['id'] ?>
    </div>
    <?php if ($relatorioEnviado): ?>
        <button class="btn btn-editar-relatorio mt-2"
            onclick="event.stopPropagation();window.location.href='form_relatorio.php?id=<?= $s['id'] ?>&editar=1'">
            Editar Relatório
        </button>
    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
</footer>

</body>
</html>