<?php
session_start();
if (!isset($_SESSION['id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'secretaria') {
    header("Location: ../index.php");
    exit;
}
require_once '../model/Secretaria.php';
$secretaria = new Secretaria();

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_edital = trim($_POST['nome_edital'] ?? '');
    $comentario = $_POST['comentario'] ?? '';
    $usuario_id = $_SESSION['id'];
    $arquivo = $_FILES['documento'] ?? null;

    if (empty($nome_edital)) {
        $mensagem = '<div class="alert alert-danger">O nome do edital é obrigatório.</div>';
    } elseif ($arquivo && $arquivo['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $permitidos = ['pdf', 'doc', 'docx'];
        if (!in_array($ext, $permitidos)) {
            $mensagem = '<div class="alert alert-danger">Formato de arquivo não permitido. Envie PDF ou DOC/DOCX.</div>';
        } else {
            $nome_arquivo = uniqid('edital_') . '.' . $ext;
            $destino = '../uploads_editais/' . $nome_arquivo;
            if (!is_dir('../uploads_editais')) {
                mkdir('../uploads_editais', 0777, true);
            }
            if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
                $ok = $secretaria->publicarEdital($usuario_id, $nome_edital, $comentario, $nome_arquivo);
                if ($ok) {
                    $mensagem = '<div class="alert alert-success">Edital publicado com sucesso!</div>';
                } else {
                    $mensagem = '<div class="alert alert-danger">Erro ao salvar no banco de dados.</div>';
                }
            } else {
                $mensagem = '<div class="alert alert-danger">Erro ao salvar o arquivo.</div>';
            }
        }
    } else {
        $mensagem = '<div class="alert alert-danger">Selecione um arquivo para upload.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Publicar Edital - Secretaria</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilo.css">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <style>
        .container { max-width: 500px; margin: 40px auto; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);}
        h1, h2 { color: #222 !important; text-align: center; margin-bottom: 24px;}
        .btn-vermelho {
            background-color: #cc1719;
            color: #fff;
            border: none;
            font-weight: bold;
            border-radius: 10px;
            padding: 10px 0;
            transition: background 0.2s;
        }
        .btn-vermelho:hover {
            background-color: #a40d24;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
        
        <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
    </div>
     <nav class="nav-menu">
        <a href="../index.php" class="nav-item">Home</a>
        <a href="../index.php" class="nav-item">Voltar</a>
        <a href="../controller/logout.php" class="nav-item">Sair</a>
    </nav>
    <div class="container">
        <h2>Publicar Novo Edital</h2>
        <?php if ($mensagem) echo $mensagem; ?>
        <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="nome_edital" class="form-label">Nome do Edital</label>
        <input type="text" class="form-control" id="nome_edital" name="nome_edital" required>
    </div>
    <div class="mb-3">
        <label for="comentario" class="form-label">Comentário (opcional)</label>
        <textarea class="form-control" id="comentario" name="comentario" rows="3"></textarea>
    </div>
    <div class="mb-3">
        <label for="documento" class="form-label">Documento do Edital (PDF, DOC, DOCX)</label>
        <input type="file" class="form-control" id="documento" name="documento" accept=".pdf,.doc,.docx" required>
    </div>
    <button type="submit" class="btn btn-vermelho w-100">Publicar Edital</button>
</form>
    </div>
    <footer class="footer mt-4">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html>