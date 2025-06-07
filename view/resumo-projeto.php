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
require_once '../model/mailer.php';  // Inclui o mailer para envio de email

if (!isset($_GET['solicitacao']) || !is_numeric($_GET['solicitacao'])) {
    die("Solicitação inválida.");
}

$diretor = new Diretor();
$solicitacao_id = (int)$_GET['solicitacao'];
$solicitacao = $diretor->buscarSolicitacaoPorId($solicitacao_id);

if (!$solicitacao) {
    die("Solicitação não encontrada.");
}

// Busca parecer existente (se houver)
$parecer = $diretor->buscarParecerPorSolicitacao($solicitacao_id);

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comentario = trim($_POST['comentario'] ?? '');
    $status_final = trim($_POST['status_final'] ?? '');
    $status_validos = ['deferido', 'indeferido', 'deferido parcialmente'];

    if (in_array(strtolower($status_final), $status_validos)) {
        try {
            $ja_existe_parecer = !empty($parecer);

            // Atualiza parecer e status
            $diretor->inserirParecer($solicitacao_id, $_SESSION['id'], $comentario, $status_final);
            $diretor->atualizarStatusSolicitacao($solicitacao_id, $status_final);

            // Recarrega parecer atualizado
            $parecer = $diretor->buscarParecerPorSolicitacao($solicitacao_id);

            // Envia e-mail apenas se ainda não existia parecer
            if (!$ja_existe_parecer) {
                $email = $solicitacao['email'] ?? '';
                $nome = $solicitacao['nome'] ?? '';
                $tipo = $solicitacao['tipo'] ?? '';

                if (!empty($email)) {
                    enviarEmailStatusHAE($email, $nome, $status_final, $tipo);
                }
            }

            // Redireciona após POST (PRG pattern) para evitar reenvio
            header("Location: resumo-projeto.php?solicitacao=$solicitacao_id&success=1");
            exit;

        } catch (Exception $e) {
            $mensagem = "Erro ao salvar parecer: " . $e->getMessage();
        }
    } else {
        $mensagem = "Status final inválido.";
    }
}

// Exibe mensagem de sucesso após redirecionamento
if (isset($_GET['success'])) {
    $mensagem = "Parecer salvo com sucesso.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    
<!-- Cabeçalho -->
    <div class="header">
        <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
        <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
        <h1>Portal do Diretor</h1>
        <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
    </div>

    <hr>
    <!-- Menu de navegação -->
    <nav class="nav-menu">
        <a href="../index.php" class="nav-item">Home</a>
        <a href="../index.php" class="nav-item">Voltar</a>
        <a href="../controller/logout.php" class="nav-item">Sair</a>
    </nav>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Resumo do Projeto - <?= htmlspecialchars($solicitacao['nome']) ?></title>
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
    --primary-color: #c8102e;
    --primary-hover: #a00d24;
    --secondary-color: #333;
    --text-color: #444;
    --light-gray: #f5f5f5;
    --white: #ffffff;
    --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
  
}
.footer {
    background-color: var(--secondary-color);
    color: var(--white);
    text-align: center;
    padding: 1.5rem;
    margin-top: auto;
    font-size: 0.9rem;
}

/* Responsividade */
@media (max-width: 1200px) {
    .side-image {
        display: none;
    }
    
    .main {
        padding: 2rem 1rem;
       
    flex: 1;
    padding: 3rem 2rem;
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    width: 100%;

    }
}

@media (max-width: 768px) {
    .menu-container {
        grid-template-columns: 1fr;
        padding: 0.5rem;
    }
    
    .nav-menu {
        justify-content: center;
        padding: 1rem;
    }
    
    .header {
        flex-direction: column;
        gap: 1rem;
        padding: 1rem;
    }
    
    .header img {
        margin: 0 !important;
    }
    
    .main h1 {
        font-size: 2rem;
    }
}

/* Animações */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.menu-box {
    animation: fadeIn 0.5s ease-out forwards;
}

.menu-box:nth-child(2) {
    animation-delay: 0.2s;
} 
.nav-menu {
    background-color: var(--primary-color);
    padding: 1rem;
    display: flex;
    justify-content: flex-end;
    gap: 2rem;
    box-shadow: var(--shadow);
}

.nav-item {
    color: var(--white);
    text-decoration: none;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    transition: var(--transition);
}

.nav-item:hover {
    background-color: var(--primary-hover);
    transform: translateY(-2px);
}
.header {
    background-color: var(--white);
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--shadow);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.header img {
    height: 50px;
    width: auto;
    transition: var(--transition);
}

.header img:hover {
    transform: scale(1.05);
}


hr {
    border: none;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    margin: 0;
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


        label {
            font-weight: 600;
            color: var(--fatec-cinza);
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
        h1, h3, h4 {
            color: var(--fatec-azul);
            margin-bottom: 20px;
            text-align: center;
        }
        .containeres { background: #fff; padding: 20px; border-radius: 8px; max-width: 900px; margin: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #ddd; vertical-align: top; }
        th { background-color: #f4f4f4; width: 200px; }
        a { display: inline-block; margin-top: 10px; color: #3a7bd5; text-decoration: none; }
        a:hover { text-decoration: underline; }

        form { margin-top: 30px; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        textarea { width: 100%; height: 100px; padding: 8px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; }
        select { width: 200px; padding: 6px; font-size: 14px; border-radius: 4px; border: 1px solid #ccc; }
        button { margin-top: 20px; padding: 10px 20px; font-size: 16px; border: none; border-radius: 4px; background-color: #3a7bd5; color: white; cursor: pointer; }
        button:hover { background-color: #2c5aa0; }
        .mensagem { margin-top: 20px; font-weight: bold; color: green; }
    </style>
</head>
<body>
    <br>
    <br>
<div class="containeres">
    <h1>Resumo da Solicitação</h1>
    <table>
        <tr><th>ID</th><td><?= htmlspecialchars($solicitacao['id']) ?></td></tr>
        <tr><th>Professor</th><td><?= htmlspecialchars($solicitacao['nome']) ?> (ID: <?= htmlspecialchars($solicitacao['professor_id']) ?>)</td></tr>
        <tr><th>Curso</th><td><?= htmlspecialchars($solicitacao['curso']) ?></td></tr>
        <tr><th>Tipo</th><td><?= htmlspecialchars($solicitacao['tipo']) ?></td></tr>
        <tr><th>Data de Envio</th><td><?= htmlspecialchars($solicitacao['data_envio']) ?></td></tr>
        <tr><th>Status</th><td><?= htmlspecialchars($solicitacao['status']) ?></td></tr>
    </table>
    <h1>Horário Preferencial</h1>
    <table>
        <tr><th>Segunda</th><td><?= htmlspecialchars($solicitacao['segunda_inicio']) ?> às <?= htmlspecialchars($solicitacao['segunda_fim']) ?></td></tr>
        <tr><th>Terça</th><td><?= htmlspecialchars($solicitacao['terca_inicio']) ?> às <?= htmlspecialchars($solicitacao['terca_fim']) ?></td></tr>
        <tr><th>Quarta</th><td><?= htmlspecialchars($solicitacao['quarta_inicio']) ?> às <?= htmlspecialchars($solicitacao['quarta_fim']) ?></td></tr>
        <tr><th>Quinta</th><td><?= htmlspecialchars($solicitacao['quinta_inicio']) ?> às <?= htmlspecialchars($solicitacao['quinta_fim']) ?></td></tr>
        <tr><th>Sexta</th><td><?= htmlspecialchars($solicitacao['sexta_inicio']) ?> às <?= htmlspecialchars($solicitacao['sexta_fim']) ?></td></tr>
        <tr><th>Sábado</th><td><?= htmlspecialchars($solicitacao['sabado_inicio']) ?> às <?= htmlspecialchars($solicitacao['sabado_fim']) ?></td></tr>
    </table>
        <h1>Justificativas</h1>
    <table>
        <tr><th>Metas</th><td><?= nl2br(htmlspecialchars($solicitacao['metas'])) ?></td></tr>
        <tr><th>Objetivos</th><td><?= nl2br(htmlspecialchars($solicitacao['objetivos'])) ?></td></tr>
        <tr><th>Justificativas</th><td><?= nl2br(htmlspecialchars($solicitacao['justificativas'])) ?></td></tr>
        <tr><th>Recursos</th><td><?= nl2br(htmlspecialchars($solicitacao['recursos'])) ?></td></tr>
        <tr><th>Resultado</th><td><?= nl2br(htmlspecialchars($solicitacao['resultado'])) ?></td></tr>
        <tr><th>Metodologia</th><td><?= nl2br(htmlspecialchars($solicitacao['metodologia'])) ?></td></tr>
    </table>

    <h2>Parecer do Diretor</h2>

    <?php if ($mensagem): ?>
        <div class="mensagem"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="comentario">Comentário</label>
        <textarea id="comentario" name="comentario" required><?= htmlspecialchars($parecer['comentario'] ?? '') ?></textarea>

        <label for="status_final">Status Final</label>
        <select id="status_final" name="status_final" required>
            <option value="" disabled <?= !isset($parecer['status_final']) ? 'selected' : '' ?>>-- Selecione --</option>
            <option value="deferido" <?= (isset($parecer['status_final']) && strtolower($parecer['status_final']) == 'deferido') ? 'selected' : '' ?>>Deferido</option>
            <option value="indeferido" <?= (isset($parecer['status_final']) && strtolower($parecer['status_final']) == 'indeferido') ? 'selected' : '' ?>>Indeferido</option>
            <option value="deferido parcialmente" <?= (isset($parecer['status_final']) && strtolower($parecer['status_final']) == 'deferido parcialmente') ? 'selected' : '' ?>>Deferido Parcialmente</option>
        </select>

        <button type="submit">Salvar Parecer</button>
    </form>

    <a href="avalia.php">← Voltar à lista</a>
</div>
<br>
<br>
<br>
<footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
</body>
</html>
