<?php 
session_start();

require_once '../model/Professor.php';
$professor = new Professor();

if (!isset($_SESSION['id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'professor') {
    header("Location: ../index.php");
    exit;
}

$nome = $_SESSION['nome'];
$editais = $professor->listarEditais();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ver Editais - Professor</title>
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
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Nenhum edital publicado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html>