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
            padding: 20px;
            margin-bottom: 0;
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
        .professor-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 cards por linha */
            gap: 48px;
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

<div class="containeres">
        <div class="cabeca">
            <h1>Minhas Solicitações de HAE</h1>
            <p class="subtitle">Veja abaixo o status das suas solicitações</p>
        </div>
        
         <div class="professor-grid">
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
                data-metas="<?= htmlspecialchars($s['metas']) ?>"
                data-objetivos="<?= htmlspecialchars($s['objetivos']) ?>"
                data-justificativas="<?= htmlspecialchars($s['justificativas']) ?>"
                data-recursos="<?= htmlspecialchars($s['recursos']) ?>"
                data-resultado="<?= htmlspecialchars($s['resultado']) ?>"
                data-metodologia="<?= htmlspecialchars($s['metodologia']) ?>"
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
        <div id="modal-alerta"></div>
        <p><strong>ID:</strong> <span id="modal-id"></span></p>
    <p><strong>Nome:</strong> <span id="modal-nome"></span></p>
    <p><strong>Tipo:</strong> <span id="modal-tipo"></span></p>
    <p><strong>Status:</strong> <span id="modal-status"></span></p>
    <p><strong>Curso:</strong> <span id="modal-curso"></span></p>
    <hr>
    <p><strong>Metas:</strong> <span id="modal-metas"></span></p>
    <p><strong>Objetivos:</strong> <span id="modal-objetivos"></span></p>
    <p><strong>Justificativas:</strong> <span id="modal-justificativas"></span></p>
    <p><strong>Recursos:</strong> <span id="modal-recursos"></span></p>
    <p><strong>Resultados:</strong> <span id="modal-resultado"></span></p>
    <p><strong>Metodologia:</strong> <span id="modal-metodologia"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="btn-editar-solicitacao">Editar</button>
        <button type="button" class="btn btn-danger d-none" id="btn-excluir-solicitacao">Excluir</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
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
    let editando = false;

    // Evento para abrir o modal e preencher os campos
    cards.forEach(card => {
        card.addEventListener('click', () => {
            document.getElementById('modal-id').textContent = card.dataset.id;
            document.getElementById('modal-nome').textContent = card.dataset.nome;
            document.getElementById('modal-tipo').textContent = card.dataset.tipo;
            document.getElementById('modal-status').textContent = card.dataset.status;
            document.getElementById('modal-curso').textContent = card.dataset.curso;
            document.getElementById('modal-metas').textContent = card.dataset.metas;
            document.getElementById('modal-objetivos').textContent = card.dataset.objetivos;
            document.getElementById('modal-justificativas').textContent = card.dataset.justificativas;
            document.getElementById('modal-recursos').textContent = card.dataset.recursos;
            document.getElementById('modal-resultado').textContent = card.dataset.resultado;
            document.getElementById('modal-metodologia').textContent = card.dataset.metodologia;
            editando = false;
            document.getElementById('btn-editar-solicitacao').textContent = "Editar";
            document.getElementById('modal-alerta').innerHTML = "";

            // Exibe ou esconde o botão Excluir conforme status
            const btnExcluir = document.getElementById('btn-excluir-solicitacao');
            if (card.dataset.status.trim().toLowerCase() === 'aguardando') {
                btnExcluir.classList.remove('d-none');
            } else {
                btnExcluir.classList.add('d-none');
            }
        });
    });

    // Evento de edição/salvamento
    document.getElementById('btn-editar-solicitacao').addEventListener('click', function() {
        if (!editando) {
            // Troca campos de texto para textarea
            ['metas','objetivos','justificativas','recursos','resultado','metodologia'].forEach(id => {
                const span = document.getElementById('modal-' + id);
                const valor = span.textContent;
                span.innerHTML = `<textarea class="form-control" id="input-${id}" rows="2">${valor}</textarea>`;
            });

            // Troca "tipo" para select
            const tipoSpan = document.getElementById('modal-tipo');
            const tipoAtual = tipoSpan.textContent.trim();
            tipoSpan.innerHTML = `
                <select class="form-select" id="input-tipo">
                    <option value="supervisionado" ${tipoAtual === 'supervisionado' ? 'selected' : ''}>Estágio Supervisionado</option>
                    <option value="Graduacao" ${tipoAtual === 'Graduacao' ? 'selected' : ''}>Trabalho de Graduação</option>
                    <option value="Cotas" ${tipoAtual === 'Cotas' ? 'selected' : ''}>Cotas de HAE – Inciso I ao IV</option>
                    <option value="Projeto de Iniciacao Cientifica" ${tipoAtual === 'Projeto de Iniciacao Cientifica' ? 'selected' : ''}>Projeto de Iniciação Científica</option>
                    <option value="Revista Prospectus" ${tipoAtual === 'Revista Prospectus' ? 'selected' : ''}>Revista Prospectus</option>
                    <option value="Divulgacao dos cursos da Fatec de Itapira" ${tipoAtual === 'Divulgacao dos cursos da Fatec de Itapira' ? 'selected' : ''}>Divulgação dos Cursos</option>
                    <option value="Captacao de alunos" ${tipoAtual === 'Captacao de alunos' ? 'selected' : ''}>Captação de Alunos</option>
                </select>
            `;

            // Troca "curso" para select
            const cursoSpan = document.getElementById('modal-curso');
            const cursoAtual = cursoSpan.textContent.trim();
            cursoSpan.innerHTML = `
                <select class="form-select" id="input-curso">
                    <option value="DSM" ${cursoAtual === 'DSM' ? 'selected' : ''}>DSM</option>
                    <option value="GE" ${cursoAtual === 'GE' ? 'selected' : ''}>GE</option>
                    <option value="GPI" ${cursoAtual === 'GPI' ? 'selected' : ''}>GPI</option>
                </select>
            `;

            this.textContent = "Salvar";
            editando = true;
        } else {
            // Envia para o PHP via fetch/ajax
            const id = document.getElementById('modal-id').textContent;
            const dados = {};
            ['metas','objetivos','justificativas','recursos','resultado','metodologia'].forEach(idCampo => {
                dados[idCampo] = document.getElementById('input-' + idCampo).value;
            });
            dados['id'] = id;
            dados['tipo'] = document.getElementById('input-tipo').value;
            dados['curso'] = document.getElementById('input-curso').value;

            fetch('../controller/editar_solicitacao.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(dados)
            })
            .then(resp => resp.json())
            .then(resp => {
                if (resp.sucesso) {
                    // Atualiza os campos no modal
                    ['metas','objetivos','justificativas','recursos','resultado','metodologia'].forEach(idCampo => {
                        document.getElementById('modal-' + idCampo).textContent = dados[idCampo];
                    });
                    document.getElementById('modal-tipo').textContent = document.getElementById('input-tipo').selectedOptions[0].text;
                    document.getElementById('modal-curso').textContent = document.getElementById('input-curso').value;
                    document.getElementById('modal-alerta').innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Alteração salva com sucesso!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                    `;
                } else {
                    document.getElementById('modal-alerta').innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Erro ao atualizar: ${resp.erro || 'Tente novamente.'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                    `;
                }
                document.getElementById('btn-editar-solicitacao').textContent = "Editar";
                editando = false;
            });
        }
    });

    // Evento para exclusão
    document.getElementById('btn-excluir-solicitacao').addEventListener('click', function() {
        if (confirm('Tem certeza que deseja excluir esta solicitação?')) {
            const id = document.getElementById('modal-id').textContent;
            fetch('../controller/excluir_solicitacao.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id })
            })
            .then(resp => resp.json())
            .then(resp => {
                if (resp.sucesso) {
                    document.getElementById('modal-alerta').innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Solicitação excluída com sucesso!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                    `;
                    setTimeout(() => location.reload(), 1500);
                } else {
                    document.getElementById('modal-alerta').innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Erro ao excluir: ${resp.erro || 'Tente novamente.'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                        </div>
                    `;
                }
            });
        }
    });
});
</script>

</body>
</html>