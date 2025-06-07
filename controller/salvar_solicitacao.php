<?php
session_start();
include '../model/Professor.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../view/dashboard_professor.php?erro=nao_autenticado");
    exit;
}

$professorModel = new Professor();
$usuario_id = $_SESSION['id'];
$professor = $professorModel->buscarPorUsuarioId($usuario_id);

if (!$professor) {
    header("Location: ../view/dashboard_professor.php?erro=professor_nao_encontrado");
    exit;
}

$professor_id = $professor['id'];

$tipo = $_POST['tipo'] ?? null;
$curso = $_POST['curso'] ?? null;
$status = 'Aguardando';

$horarios = [
    'segunda' => [$_POST['segunda_inicio'] ?? null, $_POST['segunda_fim'] ?? null],
    'terca' => [$_POST['terca_inicio'] ?? null, $_POST['terca_fim'] ?? null],
    'quarta' => [$_POST['quarta_inicio'] ?? null, $_POST['quarta_fim'] ?? null],
    'quinta' => [$_POST['quinta_inicio'] ?? null, $_POST['quinta_fim'] ?? null],
    'sexta' => [$_POST['sexta_inicio'] ?? null, $_POST['sexta_fim'] ?? null],
    'sabado' => [$_POST['sabado_inicio'] ?? null, $_POST['sabado_fim'] ?? null],
];

$metas = $_POST['metas'] ?? null;
$objetivos = $_POST['objetivos'] ?? null;
$justificativas = $_POST['justificativas'] ?? null;
$recursos = $_POST['recursos'] ?? null;
$resultado = $_POST['resultado'] ?? null;
$metodologia = $_POST['metodologia'] ?? null;

try {
    $pdo = new PDO("mysql:host=localhost;dbname=modelo", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT dia_semana, aulas FROM cargahorariaprofessor WHERE professor_id = ?");
    $stmt->execute([$professor_id]);

    $cargaAulasPorDia = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dia = strtolower($row['dia_semana']);
        $aulas = (int)$row['aulas'];
        $cargaAulasPorDia[$dia] = ($cargaAulasPorDia[$dia] ?? 0) + ($aulas * 50);
    }

    $totalMinutosSemana = 0;
    foreach ($horarios as $dia => [$inicio, $fim]) {
        $minutos_hae = 0;

        if ($inicio && $fim) {
            $inicioMin = strtotime($inicio);
            $fimMin = strtotime($fim);

            if ($inicioMin >= $fimMin) {
                header("Location: ../view/dashboard_professor.php?erro=horario_invalido&detalhe=Inicio_maior_que_fim_em_$dia");
                exit;
            }

            $minutos_hae = ($fimMin - $inicioMin) / 60;

            if (
                ($dia !== 'sabado' && $inicio >= '19:00' && $fim <= '22:30') ||
                ($dia === 'sabado' && $inicio >= '07:15' && $fim <= '12:50')
            ) {
                header("Location: ../view/dashboard_professor.php?erro=conflito_aula_regular&detalhe=$dia");
                exit;
            }
        }

        $minutos_aula = $cargaAulasPorDia[$dia] ?? 0;
        $total_dia = $minutos_aula + $minutos_hae;

        if ($total_dia > 480) {
            header("Location: ../view/dashboard_professor.php?erro=excesso_dia&detalhe=$dia");
            exit;
        }

        $totalMinutosSemana += $minutos_hae;
    }

    $totalAulasMin = array_sum($cargaAulasPorDia);
    if (($totalAulasMin + $totalMinutosSemana) > 2640) {
        header("Location: ../view/dashboard_professor.php?erro=excesso_semana");
        exit;
    }

    $sql = "INSERT INTO solicitacao_hae (
        tipo, curso, data_envio, professor_id, status,
        segunda_inicio, segunda_fim, terca_inicio, terca_fim, quarta_inicio, quarta_fim,
        quinta_inicio, quinta_fim, sexta_inicio, sexta_fim, sabado_inicio, sabado_fim,
        metas, objetivos, justificativas, recursos, resultado, metodologia, id_professor
    ) VALUES (
        :tipo, :curso, NOW(), :professor_id, :status,
        :segunda_inicio, :segunda_fim, :terca_inicio, :terca_fim, :quarta_inicio, :quarta_fim,
        :quinta_inicio, :quinta_fim, :sexta_inicio, :sexta_fim, :sabado_inicio, :sabado_fim,
        :metas, :objetivos, :justificativas, :recursos, :resultado, :metodologia, :id_professor
    )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tipo' => $tipo,
        ':curso' => $curso,
        ':professor_id' => $professor_id,
        ':status' => $status,
        ':segunda_inicio' => $horarios['segunda'][0],
        ':segunda_fim' => $horarios['segunda'][1],
        ':terca_inicio' => $horarios['terca'][0],
        ':terca_fim' => $horarios['terca'][1],
        ':quarta_inicio' => $horarios['quarta'][0],
        ':quarta_fim' => $horarios['quarta'][1],
        ':quinta_inicio' => $horarios['quinta'][0],
        ':quinta_fim' => $horarios['quinta'][1],
        ':sexta_inicio' => $horarios['sexta'][0],
        ':sexta_fim' => $horarios['sexta'][1],
        ':sabado_inicio' => $horarios['sabado'][0],
        ':sabado_fim' => $horarios['sabado'][1],
        ':metas' => $metas,
        ':objetivos' => $objetivos,
        ':justificativas' => $justificativas,
        ':recursos' => $recursos,
        ':resultado' => $resultado,
        ':metodologia' => $metodologia,
        ':id_professor' => $professor_id
    ]);

    // Tudo certo → redireciona com parâmetro de sucesso
    header("Location: ../view/dashboard_professor.php?sucesso=1");
    exit;

} catch (PDOException $e) {
    $erro = urlencode($e->getMessage());
    header("Location: ../view/dashboard_professor.php?erro=bd&detalhe=$erro");
    exit;
}
