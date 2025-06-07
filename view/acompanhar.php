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
            background: rgb(126, 0, 0);
            padding: 30px;
            border-radius: 8px;
            color: white;
        }

        .containeres {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .solicitacao-card {
            border: 1px solid #ddd;
            border-left: 8px solid #9e9e9e;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            transition: border-color 0.3s ease;
            cursor: pointer;
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

        .status {
            display: inline-flex;
            align-items: center;
            font-weight: bold;
            gap: 8px;
            font-size: 16px;
        }

        .tipo {
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
    <h1 style="text-align:center;">Acompanhar Solicitações HAE</h1>
    <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
</div>

<hr>

<nav class="nav-menu">
    <a href="../index.php" class="nav-item">Home</a>
    <a href="../controller/logout.php" class="nav-item">Sair</a>
</nav>

<div class="containeres">
    <div class="cabeca">
        <h2>Minhas Solicitações de HAE</h2>
        <p>Veja abaixo o status das suas solicitações</p>
    </div>

    <?php if (empty($solicitacoes)): ?>
        <p style="text-align:center;">Nenhuma solicitação registrada ainda.</p>
    <?php else: ?>
        <?php foreach ($solicitacoes as $s):
            $statusClasse = strtolower(str_replace(' ', '-', $s['status']));
        ?>
        <div 
            class="solicitacao-card <?= $statusClasse ?>" 
            data-bs-toggle="modal" 
            data-bs-target="#modalDetalhesSolicitacao"
            data-id="<?= $s['id'] ?>"
            data-tipo="<?= htmlspecialchars($s['tipo']) ?>"
            data-status="<?= htmlspecialchars($s['status']) ?>"
            data-curso="<?= htmlspecialchars($s['curso']) ?>"
            data-nome="<?= htmlspecialchars($s['nome']) ?>"
        >
            <div class="status">
                <span class="material-icons"><?= iconeStatus($s['status']) ?></span>
                <?= htmlspecialchars($s['status']) ?>
            </div>
            <div class="tipo">
                <strong>Tipo:</strong> <?= htmlspecialchars($s['tipo']) ?>
            </div>
            <div class="tipo">
                <strong>Curso:</strong> <?= htmlspecialchars($s['curso']) ?>
            </div>
            <div class="tipo">
                <strong>ID Solicitação:</strong> <?= $s['id'] ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="modalDetalhesSolicitacao" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Detalhes da Solicitação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <p><strong>ID:</strong> <span id="modal-id"></span></p>
        <p><strong>Nome:</strong> <span id="modal-nome"></span></p>
        <p><strong>Tipo:</strong> <span id="modal-tipo"></span></p>
        <p><strong>Status:</strong> <span id="modal-status"></span></p>
        <p><strong>Curso:</strong> <span id="modal-curso"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<footer class="footer">
    <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.solicitacao-card');

    cards.forEach(card => {
        card.addEventListener('click', () => {
            document.getElementById('modal-id').textContent = card.dataset.id;
            document.getElementById('modal-nome').textContent = card.dataset.nome;
            document.getElementById('modal-tipo').textContent = card.dataset.tipo;
            document.getElementById('modal-status').textContent = card.dataset.status;
            document.getElementById('modal-curso').textContent = card.dataset.curso;
        });
    });
});
</script>

</body>
</html>
