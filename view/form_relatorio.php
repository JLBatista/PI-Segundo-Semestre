<?php 
session_start();
include '../model/Professor.php';
$professor = new Professor();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

$idProfessor = $professor->getIdProfessorPorUsuarioId($_SESSION['id']);
$idSolicitacao = $_GET['id'] ?? null;
$editar = isset($_GET['editar']) && $_GET['editar'] == 1;

// Buscar o nome do professor
$pdo = $professor->getPdo();
$stmtNome = $pdo->prepare("SELECT u.nome 
    FROM professor p 
    JOIN usuario u ON p.usuario_id = u.id 
    WHERE p.id = :id");
$stmtNome->execute([':id' => $idProfessor]);
$nome = $stmtNome->fetchColumn();

if (!$idSolicitacao) {
    echo "Solicitação não informada.";
    exit;
}

// Buscar dados da solicitação deferida
$stmt = $pdo->prepare("SELECT metas, objetivos, justificativas FROM solicitacao_hae WHERE id = :id AND professor_id = :prof_id");
$stmt->execute([':id' => $idSolicitacao, ':prof_id' => $idProfessor]);
$solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$solicitacao) {
    echo "Solicitação não encontrada ou não autorizada.";
    exit;
}

// Se for edição, buscar dados do relatório já enviado
$relatorio = null;
if ($editar) {
    $stmtRel = $pdo->prepare("SELECT resultados, arquivo_upload FROM hae_deferidas WHERE solicitacao_hae_id = :sid AND professor_id = :pid");
    $stmtRel->execute([':sid' => $idSolicitacao, ':pid' => $idProfessor]);
    $relatorio = $stmtRel->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap e outros estilos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/fa068c530f.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="estilo.css">
    <title>Portal do Professor - Formulário</title>
    <style>
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
        .container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        h1, h3, h4 {
            color: var(--primary-color);
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            font-weight: 600;
            color: var(--fatec-cinza);
        }
        .form-select, .form-control {
            border-radius: 10px;
            border: 1px solid #ccc;
        }
        .form-select:focus, .form-control:focus {
            border-color: var(--fatec-vermelho);
            box-shadow: 0 0 5px rgba(204, 23, 25, 0.5);
        }
        textarea {
            resize: vertical;
        }
        .card-header {
            background-color: var(--fatec-azul);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 15px;
        }
        .btn-primary {
            background-color: var(--fatec-vermelho);
            border: none;
            padding: 10px 25px;
            font-weight: bold;
            border-radius: 10px;
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
        <h1>Portal de Relatórios</h1>
        <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
    </div>
    <hr>
    <!-- Menu de navegação -->
    <nav class="nav-menu">
        <a href="../index.php" class="nav-item">Home</a>
        <a href="../index.php" class="nav-item">Voltar</a>
        <a href="../controller/logout.php" class="nav-item">Sair</a>
    </nav>
    <div id="userTypeIndicator" class="user-type"></div>
    <div class="container">
        <div id="professorContent">
            <h1>Bem vindo <?php echo htmlspecialchars($nome); ?>!</h1>
            
            <form action="../controller/salvar_relatorio.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="solicitacao_hae_id" value="<?= htmlspecialchars($idSolicitacao) ?>">
                <input type="hidden" name="professor_id" value="<?= htmlspecialchars($idProfessor) ?>">

                <div class="mb-3">
                    <label class="form-label"><strong>Metas</strong></label>
                    <div class="form-control" readonly style="background:#f8f9fa"><?= nl2br(htmlspecialchars($solicitacao['metas'])) ?></div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Objetivos</strong></label>
                    <div class="form-control" readonly style="background:#f8f9fa"><?= nl2br(htmlspecialchars($solicitacao['objetivos'])) ?></div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Justificativas</strong></label>
                    <div class="form-control" readonly style="background:#f8f9fa"><?= nl2br(htmlspecialchars($solicitacao['justificativas'])) ?></div>
                </div>
                <hr>
                <div class="mb-3">
                    <label for="resultado" class="form-label"><strong>Resultado (atingido?)</strong></label>
                    <textarea class="form-control" id="resultado" name="resultado" rows="3" required placeholder="Descreva se os resultados foram atingidos"><?= $editar && $relatorio ? htmlspecialchars($relatorio['resultados']) : '' ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="arquivo_upload" class="form-label"><strong>Metodologia (anexe o arquivo)</strong></label>
                    <?php if ($editar && $relatorio && !empty($relatorio['arquivo_upload'])): ?>
                        <div class="mb-2">
                            <span class="text-success">Arquivo atual:</span>
                            <a href="../uploads_relatorio/<?= htmlspecialchars($relatorio['arquivo_upload']) ?>" target="_blank">
                                <?= htmlspecialchars($relatorio['arquivo_upload']) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="arquivo_upload" name="arquivo_upload" accept=".pdf,.doc,.docx,.zip,.rar" <?= $editar ? '' : 'required' ?>>
                    <small class="text-muted">Formatos aceitos: PDF, DOC, DOCX, ZIP, RAR<?= $editar ? '. Deixe em branco para manter o arquivo atual.' : '' ?></small>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary"><?= $editar ? 'Atualizar Relatório' : 'Enviar Relatório' ?></button>
                </div>
            </form>
        </div>
    </div>
    <!-- Rodapé -->
    <footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html>