<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (!isset($dados['id'])) {
        echo json_encode(['sucesso' => false, 'erro' => 'ID não informado']);
        exit;
    }

    require_once '../model/Professor.php';
    $professor = new Professor();

    $idSolicitacao = $dados['id'];
    $idProfessor = $professor->getIdProfessorPorUsuarioId($_SESSION['id']);

    // Exclui apenas se a solicitação for do professor logado
    $pdo = $professor->getPdo();
    $sql = "DELETE FROM solicitacao_hae WHERE id = :id AND professor_id = :professor_id";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        ':id' => $idSolicitacao,
        ':professor_id' => $idProfessor
    ]);

    if ($ok && $stmt->rowCount() > 0) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Não foi possível excluir.']);
    }
    exit;
}
echo json_encode(['sucesso' => false, 'erro' => 'Método inválido']);