<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../view/relatorio.php');
    exit;
}

require_once '../model/Professor.php';
$professor = new Professor();

$idProfessor = $professor->getIdProfessorPorUsuarioId($_SESSION['id']);
$idSolicitacao = $_POST['solicitacao_hae_id'] ?? null;
$resultado = $_POST['resultado'] ?? '';
$status_final = 'Deferido'; // ou defina conforme sua lógica

// Validação básica
if (!$idSolicitacao || !$idProfessor || empty($resultado)) {
    echo "Dados obrigatórios não informados.";
    exit;
}

// Upload do arquivo
$arquivo_nome = null;
if (isset($_FILES['arquivo_upload']) && $_FILES['arquivo_upload']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['arquivo_upload']['name'], PATHINFO_EXTENSION));
    $permitidos = ['pdf', 'doc', 'docx', 'zip', 'rar'];
    if (!in_array($ext, $permitidos)) {
        echo "Formato de arquivo não permitido.";
        exit;
    }
    $pasta = "../uploads_relatorio/";
    if (!is_dir($pasta)) mkdir($pasta, 0777, true);
    $arquivo_nome = uniqid('metodologia_') . '.' . $ext;
    move_uploaded_file($_FILES['arquivo_upload']['tmp_name'], $pasta . $arquivo_nome);
}

$pdo = $professor->getPdo();

// Função para saber se já existe relatório
function relatorioExiste($pdo, $idSolicitacao, $idProfessor) {
    $stmt = $pdo->prepare("SELECT id FROM hae_deferidas WHERE solicitacao_hae_id = :sid AND professor_id = :pid");
    $stmt->execute([':sid' => $idSolicitacao, ':pid' => $idProfessor]);
    return $stmt->fetchColumn();
}

$relatorioId = relatorioExiste($pdo, $idSolicitacao, $idProfessor);

if ($relatorioId) {
    // UPDATE: se não enviou novo arquivo, mantém o antigo
    if (!$arquivo_nome) {
        $stmtOld = $pdo->prepare("SELECT arquivo_upload FROM hae_deferidas WHERE id = :id");
        $stmtOld->execute([':id' => $relatorioId]);
        $arquivo_nome = $stmtOld->fetchColumn();
    }
    $sql = "UPDATE hae_deferidas 
        SET resultados = :resultados, arquivo_upload = :arquivo_upload, status_final = :status_final
        WHERE solicitacao_hae_id = :solicitacao_hae_id AND professor_id = :professor_id";
$stmt = $pdo->prepare($sql);
$ok = $stmt->execute([
    ':resultados' => $resultado,
    ':arquivo_upload' => $arquivo_nome,
    ':status_final' => $status_final,
    ':solicitacao_hae_id' => $idSolicitacao,
    ':professor_id' => $idProfessor
]);
} else {
    // INSERT
    $sql = "INSERT INTO hae_deferidas 
            (solicitacao_hae_id, professor_id, resultados, arquivo_upload, status_final)
            VALUES (:solicitacao_hae_id, :professor_id, :resultados, :arquivo_upload, :status_final)";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        ':solicitacao_hae_id' => $idSolicitacao,
        ':professor_id' => $idProfessor,
        ':resultados' => $resultado,
        ':arquivo_upload' => $arquivo_nome,
        ':status_final' => $status_final
    ]);
}

if ($ok) {
    header('Location: ../view/relatorio.php?msg=relatorio_salvo');
    exit;
} else {
    echo "Erro ao salvar relatório.";
}