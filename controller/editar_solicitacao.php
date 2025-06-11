<?php
// filepath: c:\xampp\htdocs\Testes\view\editar_solicitacao.php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    require_once '../model/Professor.php';
    $professor = new Professor();

    // Segurança: só permite editar se for o dono da solicitação
    $idSolicitacao = $dados['id'];
    $idProfessor = $professor->getIdProfessorPorUsuarioId($_SESSION['id']);

    // Aqui você pode validar se o idProfessor é dono da solicitação

    $pdo = $professor->getPdo();
    $sql = "UPDATE solicitacao_hae SET 
    tipo = :tipo,
    curso = :curso,
    metas = :metas,
    objetivos = :objetivos,
    justificativas = :justificativas,
    recursos = :recursos,
    resultado = :resultado,
    metodologia = :metodologia
    WHERE id = :id AND professor_id = :professor_id";
$stmt = $pdo->prepare($sql);
$ok = $stmt->execute([
    ':tipo' => $dados['tipo'],
    ':curso' => $dados['curso'],
    ':metas' => $dados['metas'],
    ':objetivos' => $dados['objetivos'],
    ':justificativas' => $dados['justificativas'],
    ':recursos' => $dados['recursos'],
    ':resultado' => $dados['resultado'],
    ':metodologia' => $dados['metodologia'],
    ':id' => $idSolicitacao,
    ':professor_id' => $idProfessor
]);
    echo json_encode(['sucesso' => $ok]);
    exit;
}
echo json_encode(['sucesso' => false, 'erro' => 'Método inválido']);
