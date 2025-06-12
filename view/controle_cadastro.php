<?php
session_start();

// Permitir acesso apenas para administradores
if (!isset($_SESSION['id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

// Conexão direta
$host = 'localhost';
$dbname = 'modelo'; 
$user = 'root'; 
$pass = '';     

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Inclui o model Administrador
require_once '../model/Administrador.php';
$admin = new Administrador();

// Processa edição via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_usuario'])) {
    $id = $_POST['id'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $tipo = $_POST['tipo_usuario'] ?? '';
    $senha = $_POST['senha'] ?? null;

    $ok = $admin->editarUsuario($id, $nome, $email, $tipo, $senha);
    echo json_encode(['sucesso' => $ok]);
    exit;
}

// Processa exclusão via GET
if (isset($_GET['excluir_usuario'])) {
    $id = $_GET['excluir_usuario'];
    $admin->excluirUsuario($id);
    header("Location: controle_cadastro.php");
    exit;
}

// Busca todos os usuários
$stmt = $pdo->query("SELECT id, nome, email, tipo_usuario FROM usuario ORDER BY nome");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Cadastros - SISESCOLAR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="estilo.css">
    <style>
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
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        h1, h4 {
            color: var(--fatec-vermelho);
            margin-bottom: 20px;
            text-align: center;
        }
        .table thead {
            background: var(--fatec-azul);
            color: #fff;
        }
        .table-striped>tbody>tr:nth-of-type(odd)>* {
            background-color: #f8f9fa;
        }
        .nav-menu {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
        <h1>Controle de Cadastros</h1>
        <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
    </div>
    <hr>
    <!-- Menu de navegação -->
    <nav class="nav-menu">
        <a href="../index.php" class="nav-item">Home</a>
        <a href="../view/admin.php" class="nav-item">Voltar</a>
        <a href="../controller/logout.php" class="nav-item">Sair</a>
    </nav>
    <div class="container">
        <h4>Usuários cadastrados no sistema</h4>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo de Usuário</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($usuarios) > 0): ?>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['id']) ?></td>
                            <td><?= htmlspecialchars($u['nome']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <?php
                                switch ($u['tipo_usuario']) {
                                    case 'professor': echo 'Professor'; break;
                                    case 'direcao': echo 'Diretor'; break;
                                    case 'secretaria': echo 'Secretaria'; break;
                                    case 'administrador': echo 'Administrador'; break;
                                    default: echo ucfirst($u['tipo_usuario']);
                                }
                                ?>
                            </td>
                            <td>
                                <button 
                                    class="btn btn-sm btn-warning btn-editar-usuario"
                                    data-id="<?= $u['id'] ?>"
                                    data-nome="<?= htmlspecialchars($u['nome']) ?>"
                                    data-email="<?= htmlspecialchars($u['email']) ?>"
                                    data-tipo="<?= $u['tipo_usuario'] ?>"
                                >Editar</button>
                                <a href="?excluir_usuario=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Nenhum usuário cadastrado.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form id="formEditarUsuario" method="post">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuário</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id" id="edit-id">
              <input type="hidden" name="editar_usuario" value="1">
              <div class="mb-3">
                <label for="edit-nome" class="form-label">Nome</label>
                <input type="text" class="form-control" name="nome" id="edit-nome" required>
              </div>
              <div class="mb-3">
                <label for="edit-email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="edit-email" required>
              </div>
              <div class="mb-3">
                <label for="edit-tipo" class="form-label">Tipo de Usuário</label>
                <select class="form-select" name="tipo_usuario" id="edit-tipo" required>
                  <option value="professor">Professor</option>
                  <option value="direcao">Diretor</option>
                  <option value="secretaria">Secretaria</option>
                  <option value="administrador">Administrador</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="edit-senha" class="form-label">Nova Senha (opcional)</label>
                <input type="password" class="form-control" name="senha" id="edit-senha">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <footer class="footer mt-4">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.btn-editar-usuario').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-nome').value = this.dataset.nome;
            document.getElementById('edit-email').value = this.dataset.email;
            document.getElementById('edit-tipo').value = this.dataset.tipo;
            document.getElementById('edit-senha').value = '';
            var modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
            modal.show();
        });
    });

    // Envio do formulário via AJAX
    document.getElementById('formEditarUsuario').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        var formData = new FormData(form);
        fetch('controle_cadastro.php', {
            method: 'POST',
            body: formData
        })
        .then(resp => resp.json())
        .then(data => {
            if (data.sucesso) {
                location.reload();
            } else {
                alert('Erro ao editar usuário.');
            }
        });
    });
    </script>
</body>
</html>