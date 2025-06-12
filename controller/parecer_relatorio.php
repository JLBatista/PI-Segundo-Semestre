<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo_usuario'] !== 'direcao') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../view/avalia_relatorio.php");
    exit;
}

require_once '../model/Diretor.php';
$diretor = new Diretor();

$relatorioId = $_POST['relatorio_id'] ?? null;
$parecer = $_POST['parecer'] ?? null;

if (!$relatorioId || !in_array($parecer, ['aprovado', 'reprovado'])) {
    header("Location: ../view/avalia_relatorio.php?msg=erro");
    exit;
}

// Atualiza o campo parecer na tabela hae_deferidas
$pdo = $diretor->getPdo();
$stmt = $pdo->prepare("UPDATE hae_deferidas SET status_final = :status WHERE id = :id");
$ok = $stmt->execute([
    ':status' => $parecer, // "aprovado" ou "reprovado"
    ':id' => $relatorioId
]);

if ($ok) {
    header("Location: ../view/avalia_relatorio.php?msg=parecer_salvo");
    exit;
} else {
    header("Location: ../view/avalia_relatorio.php?msg=erro");
    exit;
}