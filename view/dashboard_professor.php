<?php 
session_start();

include '../model/Professor.php';
$professor = new Professor();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

$nome = $_SESSION['nome'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Estilos externos -->
    <link rel="stylesheet" href="https://fatecitapira.cps.sp.gov.br/wp-content/themes/tema-cps/css/sao-paulo/topo-padrao-govsp.min.css">
    <link rel="stylesheet" href="https://fatecitapira.cps.sp.gov.br/wp-content/themes/tema-cps/css/sao-paulo/barra-contraste-govsp.min.css">
    <link rel="stylesheet" href="https://fatecitapira.cps.sp.gov.br/wp-content/themes/tema-cps/css/sao-paulo/rodape-padrao-govsp.min.css">
    <script src="https://kit.fontawesome.com/fa068c530f.js" crossorigin="anonymous"></script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Estilo próprio -->
    <link rel="stylesheet" href="estilo.css">

    <title>Dashboard HAE</title>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Toasts -->
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
            <?php if (isset($_GET['sucesso'])): ?>
                <div class="toast align-items-center text-bg-success border-0 show" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">Solicitação enviada com sucesso!</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            <?php endif; ?>

           <?php if (isset($_GET['sucesso'])): ?>
    <div class="toast align-items-center text-bg-success border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                Solicitação enviada com sucesso!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
<?php endif; ?>
        </div>
    </div>

    <!-- Cabeçalho -->
    <div class="header">
        <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
        <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
    </div>

    <!-- Menu de navegação -->
    <nav class="nav-menu">
        <a href="../index.php" class="nav-item">Home</a>
        <a href="../index.php" class="nav-item">Voltar</a>
        <a href="../controller/logout.php" class="nav-item">Sair</a>
    </nav>

    <!-- Conteúdo principal -->
    <main class="main flex-grow-1">
        <h1>Menu Professores</h1>

        <div class="menu-container">
            <div class="menu-box" onclick="window.location.href='formulario.php'">
                <img src="../assets/icon-doc-gray.png" alt="Inscrição" class="menu-icon">
                <h2>INSCRIÇÃO</h2>
                <p>Realize sua inscrição para atividades</p>
            </div>

            <div class="menu-box" onclick="window.location.href='acompanhar.php'">
                <img src="../assets/icon-com-gray.png" alt="Acompanhar" class="menu-icon">
                <h2>ACOMPANHAR</h2>
                <p>Acompanhe suas inscrições e status</p>
            </div>
        </div>
    </main>

    <!-- Rodapé fixo ao fim -->
    <footer class="footer mt-auto">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>

</body>
</html>
