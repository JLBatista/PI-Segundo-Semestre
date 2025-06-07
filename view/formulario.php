<?php 
session_start();

include '../model/Professor.php';
$professor = new Professor();

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

$dadosProfessor = $professor->buscarDadosBasicosPorUsuarioId($_SESSION['id']);

if ($dadosProfessor) {
    $nome = $dadosProfessor['nome'];
    $rg = $dadosProfessor['rg'];
    $email = $dadosProfessor['email'];
} else {
    echo "Erro: Professor não encontrado.";
    exit;
}

// Repopular campos em caso de erro
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap e outros estilos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/fa068c530f.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="estilo.css">
    <title>Portal do Professor - Formulário</title>
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
        .container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        h1, h3, h4 {
            color: var(--primary-color);
            margin-bottom: 20px;
            text-align: center;
        }
        label {
            font-weight: 600;
            color: var(--fatec-cinza);
        }
        .form-select, .form-control {
            border-radius: 10px;
            border: 1px solid #ccc;
        }
        .form-select:focus, .form-control:focus {
            border-color: var(--fatec-vermelho);
            box-shadow: 0 0 5px rgba(204, 23, 25, 0.5);
        }
        textarea {
            resize: vertical;
        }
        .card-header {
            background-color: var(--fatec-azul);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 15px;
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
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <img src="../assets/logo-fatec_itapira.png" alt="Logo Fatec Itapira">
        <h1>Portal do Professor</h1>
        <img src="../assets/hora.png" alt="Logo Hora+" style="height: 50px;">
    </div>
    <hr>
    <!-- Menu de navegação -->
    <nav class="nav-menu">
        <a href="../index.php" class="nav-item">Home</a>
        <a href="../index.php" class="nav-item">Voltar</a>
        <a href="../controller/logout.php" class="nav-item">Sair</a>
    </nav>
    <div id="userTypeIndicator" class="user-type"></div>
    <div class="container">
        <div id="professorContent">
            <h1>Bem vindo <?php echo htmlspecialchars($nome); ?>!</h1>
            
            <!-- Mensagens de erro do PHP (GET) -->
            <div id="mensagens-erro">
            <?php
            if (isset($_GET['msg']) && $_GET['msg'] == 'erro') {
                $motivo = $_GET['motivo'] ?? 'desconhecido';
                $dia = $_GET['dia'] ?? '';
                $detalhe = $_GET['detalhe'] ?? '';
                $mensagens = [
                    'limite_semanal' => 'O limite semanal de 44h foi excedido.',
                    'limite_diario_segunda' => 'O limite diário de 8h foi excedido na segunda-feira.',
                    'limite_diario_terca' => 'O limite diário de 8h foi excedido na terça-feira.',
                    'limite_diario_quarta' => 'O limite diário de 8h foi excedido na quarta-feira.',
                    'limite_diario_quinta' => 'O limite diário de 8h foi excedido na quinta-feira.',
                    'limite_diario_sexta' => 'O limite diário de 8h foi excedido na sexta-feira.',
                    'limite_diario_sabado' => 'O limite diário de 8h foi excedido no sábado.',
                    'horario_invalido_segunda' => 'Horário inválido na segunda-feira.',
                    'horario_invalido_terca' => 'Horário inválido na terça-feira.',
                    'horario_invalido_quarta' => 'Horário inválido na quarta-feira.',
                    'horario_invalido_quinta' => 'Horário inválido na quinta-feira.',
                    'horario_invalido_sexta' => 'Horário inválido na sexta-feira.',
                    'horario_invalido_sabado' => 'Horário inválido no sábado.',
                    'conflito_aula_regular' => 'Conflito com aula regular em ' . htmlspecialchars($dia) . '.',
                    'bd' => 'Erro no banco de dados: ' . htmlspecialchars($detalhe),
                    'horario_proibido_segunda' => 'HAE não pode ser solicitada na segunda durante o horário de aula (19:00 às 22:30).',
                    'horario_proibido_terca' => 'HAE não pode ser solicitada na terça durante o horário de aula (19:00 às 22:30).',
                    'horario_proibido_quarta' => 'HAE não pode ser solicitada na quarta durante o horário de aula (19:00 às 22:30).',
                    'horario_proibido_quinta' => 'HAE não pode ser solicitada na quinta durante o horário de aula (19:00 às 22:30).',
                    'horario_proibido_sexta' => 'HAE não pode ser solicitada na sexta durante o horário de aula (19:00 às 22:30).',
                    'horario_proibido_sabado' => 'HAE não pode ser solicitada no sábado durante o horário de aula (07:15 às 12:50).',
                ];
                $mensagem = $mensagens[$motivo] ?? 'Erro desconhecido.';
                echo "<div class='alert alert-danger'>$mensagem</div>";
            }
            ?>
            </div>

            <form action="../controller/salvar_solicitacao.php" method="POST" id="form-inscricao" class="needs-validation" novalidate>
                <!-- Tipo de HAE -->
                <div class="mb-3">
                    <label for="tipo" class="form-label">Qual tipo de HAE Solicitada?</label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <option value="" disabled <?php echo empty($form_data['tipo']) ? 'selected' : ''; ?>>Escolha a HAE desejada</option>
                        <option value="supervisionado" <?php if(($form_data['tipo'] ?? '') == 'supervisionado') echo 'selected'; ?>>Estágio Supervisionado</option>
                        <option value="Graduacao" <?php if(($form_data['tipo'] ?? '') == 'Graduacao') echo 'selected'; ?>>Trabalho de Graduação</option>
                        <option value="Cotas" <?php if(($form_data['tipo'] ?? '') == 'Cotas') echo 'selected'; ?>>Cotas de HAE – Inciso I ao IV</option>
                        <option value="Projeto de Iniciação Científica" <?php if(($form_data['tipo'] ?? '') == 'Projeto de Iniciação Científica') echo 'selected'; ?>>Projeto de Iniciação Científica</option>
                        <option value="Revista Prospectus" <?php if(($form_data['tipo'] ?? '') == 'Revista Prospectus') echo 'selected'; ?>>Revista Prospectus</option>
                        <option value="Divulgação dos cursos da Fatec de Itapira" <?php if(($form_data['tipo'] ?? '') == 'Divulgação dos cursos da Fatec de Itapira') echo 'selected'; ?>>Divulgação dos Cursos</option>
                        <option value="Captação de alunos" <?php if(($form_data['tipo'] ?? '') == 'Captação de alunos') echo 'selected'; ?>>Captação de Alunos</option>
                    </select>
                    <div class="invalid-feedback">Por favor, escolha um tipo de HAE.</div>
                </div>
                <div class="mb-3">
                    <label for="curso" class="form-label">Curso:</label>
                    <select class="form-select" id="curso" name="curso" required>
                        <option value="" disabled <?php echo empty($form_data['curso']) ? 'selected' : ''; ?>>Escolha o curso</option>
                        <option value="DSM" <?php if(($form_data['curso'] ?? '') == 'DSM') echo 'selected'; ?>>Desenvolvimento de Software Multiplataforma (DSM)</option>
                        <option value="GPI" <?php if(($form_data['curso'] ?? '') == 'GPI') echo 'selected'; ?>>Gestão de Produção Industrial (GPI)</option>
                        <option value="GE" <?php if(($form_data['curso'] ?? '') == 'GE') echo 'selected'; ?>>Gestão Empresarial (GE)</option>
                    </select>
                    <div class="invalid-feedback">Por favor, escolha um curso.</div>
                </div>
                <!-- Horários Dinâmicos -->
                <div class="row">
                    <h3>Selecione o Horário Desejado</h3>
                    <?php
                    $dias = [
                        'segunda' => 'Segunda-feira',
                        'terca' => 'Terça-feira',
                        'quarta' => 'Quarta-feira',
                        'quinta' => 'Quinta-feira',
                        'sexta' => 'Sexta-feira',
                        'sabado' => 'Sábado'
                    ];
                    foreach ($dias as $dia => $label) {
                        $inicio = htmlspecialchars($form_data[$dia . '_inicio'] ?? '');
                        $fim = htmlspecialchars($form_data[$dia . '_fim'] ?? '');
                        echo "
                        <div class='col-sm-6 mb-3'>
                            <label for='{$dia}_inicio' class='form-label'><p>{$label} - Início</p></label>
                            <input type='time' class='form-control' id='{$dia}_inicio' name='{$dia}_inicio' value='{$inicio}'>
                        </div>
                        <div class='col-sm-6 mb-3'>
                            <label for='{$dia}_fim' class='form-label'><p>{$label} - Fim</p></label>
                            <input type='time' class='form-control' id='{$dia}_fim' name='{$dia}_fim' value='{$fim}'>
                        </div>
                        ";
                    }
                    ?>
                </div>
                <hr class="my-4">
                <!-- Detalhamento do Projeto -->
                <div class="mb-3">
                    <label for="metas" class="form-label">Metas Relacionadas ao Projeto</label>
                    <textarea class="form-control" id="metas" name="metas" rows="3" required><?php echo htmlspecialchars($form_data['metas'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="objetivos" class="form-label">Objetivos do Projeto – Detalhamento</label>
                    <textarea class="form-control" id="objetivos" name="objetivos" rows="3" required><?php echo htmlspecialchars($form_data['objetivos'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="justificativas" class="form-label">Justificativas do Projeto</label>
                    <textarea class="form-control" id="justificativas" name="justificativas" rows="3" required><?php echo htmlspecialchars($form_data['justificativas'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="recursos" class="form-label">Recursos Materiais e Humanos</label>
                    <textarea class="form-control" id="recursos" name="recursos" rows="3" required><?php echo htmlspecialchars($form_data['recursos'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="resultado" class="form-label">Resultado Esperado</label>
                    <textarea class="form-control" id="resultado" name="resultado" rows="3" required><?php echo htmlspecialchars($form_data['resultado'] ?? ''); ?></textarea>
                </div>
                <div class="mb-4">
                    <label for="metodologia" class="form-label">Metodologia</label>
                    <textarea class="form-control" id="metodologia" name="metodologia" rows="3" required><?php echo htmlspecialchars($form_data['metodologia'] ?? ''); ?></textarea>
                </div>
                <!-- Botão -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Enviar Solicitação</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Rodapé -->
    <footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
    <!-- Scripts -->
    <script>
    document.getElementById('form-inscricao').addEventListener('submit', function(e) {
        let erros = [];
        // Validação dos campos obrigatórios
        const tipo = document.getElementById('tipo').value;
        const curso = document.getElementById('curso').value;
        if (!tipo) erros.push("Escolha o tipo de HAE.");
        if (!curso) erros.push("Escolha o curso.");
        // Validação dos horários para todos os dias da semana
        const dias = [
            {nome: 'segunda', label: 'Segunda-feira'},
            {nome: 'terca', label: 'Terça-feira'},
            {nome: 'quarta', label: 'Quarta-feira'},
            {nome: 'quinta', label: 'Quinta-feira'},
            {nome: 'sexta', label: 'Sexta-feira'},
            {nome: 'sabado', label: 'Sábado'}
        ];
        dias.forEach(function(dia) {
            const inicio = document.getElementById(dia.nome + '_inicio').value;
            const fim = document.getElementById(dia.nome + '_fim').value;
            if (inicio && fim && inicio >= fim) {
                erros.push(`O horário de início deve ser menor que o de fim em ${dia.label}.`);
            }
        });
        // Validação dos campos de texto obrigatórios
        ['metas', 'objetivos', 'justificativas', 'recursos', 'resultado', 'metodologia'].forEach(id => {
            if (!document.getElementById(id).value.trim()) {
                erros.push("Preencha o campo " + id + ".");
            }
        });
        // Se houver erros, exibe e impede o envio
        if (erros.length > 0) {
            e.preventDefault();
            let msg = erros.map(e => `<div class="alert alert-danger">${e}</div>`).join('');
            document.getElementById('mensagens-erro').innerHTML = msg;
            window.scrollTo(0,0);
        }
    });
    </script>
</body>
</html