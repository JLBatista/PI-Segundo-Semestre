<?php
session_start();
if (!isset($_SESSION['id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'secretaria') {
    header("Location: ../index.php");
    exit;
}
require_once '../model/Secretaria.php';
$secretaria = new Secretaria();

// Atualização do edital (edição via popup)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edital_id'])) {
    $id = $_POST['edital_id'];
    $nome_edital = trim($_POST['nome_edital_edit'] ?? '');
    $comentario = $_POST['comentario_edit'] ?? '';
    $documento = null;

    if (isset($_FILES['documento_edit']) && $_FILES['documento_edit']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['documento_edit']['name'], PATHINFO_EXTENSION));
        $permitidos = ['pdf', 'doc', 'docx'];
        if (in_array($ext, $permitidos)) {
            $nome_arquivo = uniqid('edital_') . '.' . $ext;
            $destino = '../uploads_editais/' . $nome_arquivo;
            if (move_uploaded_file($_FILES['documento_edit']['tmp_name'], $destino)) {
                $documento = $nome_arquivo;
            }
        }
    }

    $secretaria->atualizarEdital($id, $nome_edital, $comentario, $documento);
    // Recarrega os editais após alteração
    $editais = $secretaria->listarEditais();
    echo "<script>window.onload = function(){fecharPopupEditar();}</script>";
} else {
    $editais = $secretaria->listarEditais();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ver Editais - Secretaria</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilo.css">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <style>
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);}
        h1 { color: #222; text-align: center; margin-bottom: 24px;}
        .table thead { background: #003e7e; color: #fff; }
        .table-striped>tbody>tr:nth-of-type(odd)>* { background-color: #f8f9fa; }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            flex: 1 0 auto;
        }
        .footer {
            flex-shrink: 0;
            text-align: center;
            padding: 16px 0 8px 0;
            margin-top: 32px;
            border-top: 1px solid #eee;
        }
        .header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    padding: 18px 32px; /* Adiciona espaço nas laterais */
    gap: 16px;
}
.header h1 {
    flex: 1;
    text-align: center;
    margin: 0;
    color: #222;
    font-size: 2rem;
}
        .header img {
            height: 50px;
        }
        .header-title-center {
            position: absolute;
            left: 0;
            right: 0;
            text-align: center;
            pointer-events: none;
        }
        .header-title-center h1 {
            margin: 0;
            color: #222;
            font-size: 2rem;
        }
        .btn-vermelho {
            background-color: #cc1719;
            color: #fff;
            border: none;
            font-weight: bold;
            border-radius: 10px;
            padding: 6px 18px;
            transition: background 0.2s;
        }
        .btn-vermelho:hover {
            background-color: #a40d24;
        }
        #popup-editar {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.4);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        #popup-editar .popup-content {
            background: #fff;
            padding: 32px 24px;
            border-radius: 12px;
            max-width: 400px;
            width: 90%;
            position: relative;
        }
        #popup-editar .close-btn {
            position: absolute;
            top: 8px;
            right: 12px;
            border: none;
            background: none;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="header">
    <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira" style="height: 50px;">
    <h1 style="flex:1; text-align:center; margin:0; color:#222; font-size:2rem;">Lista de Editais Publicados</h1>
    <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
</div>

    <nav class="nav-menu">
        <a href="../index.php" class="nav-item">Home</a>
        <a href="../index.php" class="nav-item">Voltar</a>
        <a href="../controller/logout.php" class="nav-item">Sair</a>
    </nav>

    <div class="container">
        <h2 style="color:#222;">Editais Publicados</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Edital</th>
                        <th>Comentário</th>
                        <th>Data de Postagem</th>
                        <th>Documento</th>
                        <th>Publicado por</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($editais) > 0): ?>
                        <?php foreach ($editais as $edital): ?>
                            <tr>
                                <td><?= htmlspecialchars($edital['id']) ?></td>
                                <td><?= htmlspecialchars($edital['nome_edital']) ?></td>
                                <td><?= nl2br(htmlspecialchars($edital['comentario'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($edital['data_postagem'])) ?></td>
                                <td>
                                    <a href="../uploads_editais/<?= urlencode($edital['documento']) ?>" target="_blank" class="btn btn-vermelho btn-sm">Ver Documento</a>
                                </td>
                                <td><?= htmlspecialchars($edital['secretaria_nome']) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning"
                                        onclick="abrirPopupEditar(
                                            <?= $edital['id'] ?>,
                                            '<?= htmlspecialchars(addslashes($edital['nome_edital'])) ?>',
                                            '<?= htmlspecialchars(addslashes($edital['comentario'])) ?>'
                                        )">Editar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Nenhum edital publicado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Popup de edição -->
    <div id="popup-editar">
        <div class="popup-content">
            <button onclick="fecharPopupEditar()" class="close-btn">&times;</button>
            <form id="form-editar-edital" enctype="multipart/form-data" method="post">
                <input type="hidden" name="edital_id" id="edital_id">
                <div class="mb-3">
                    <label for="nome_edital_edit" class="form-label">Nome do Edital</label>
                    <input type="text" class="form-control" name="nome_edital_edit" id="nome_edital_edit" required>
                </div>
                <div class="mb-3">
                    <label for="comentario_edit" class="form-label">Comentário</label>
                    <textarea class="form-control" name="comentario_edit" id="comentario_edit" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="documento_edit" class="form-label">Novo Documento (opcional)</label>
                    <input type="file" class="form-control" name="documento_edit" id="documento_edit" accept=".pdf,.doc,.docx">
                </div>
                <button type="submit" class="btn btn-success w-100">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>

    <script>
    function abrirPopupEditar(id, nome, comentario) {
        document.getElementById('popup-editar').style.display = 'flex';
        document.getElementById('edital_id').value = id;
        document.getElementById('nome_edital_edit').value = nome;
        document.getElementById('comentario_edit').value = comentario.replace(/\\n/g, "\n");
    }
    function fecharPopupEditar() {
        document.getElementById('popup-editar').style.display = 'none';
    }
    </script>
</body>
</html>