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
    $email = $dadosProfessor['email']; // substitui "matricula"
} else {
    echo "Erro: Professor não encontrado.";
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] == 'erro') {
    $motivo = $_GET['motivo'] ?? 'desconhecido';
    $mensagens = [
        'limite_semanal' => 'O limite semanal de 44h foi excedido.',
        'horario_proibido_segunda' => 'HAE não pode ser solicitada na segunda durante o horário de aula (19:00 às 22:30).',
        'horario_proibido_terca' => 'HAE não pode ser solicitada na terça durante o horário de aula (19:00 às 22:30).',
        'horario_proibido_quarta' => 'HAE não pode ser solicitada na quarta durante o horário de aula (19:00 às 22:30).',
        'horario_proibido_quinta' => 'HAE não pode ser solicitada na quinta durante o horário de aula (19:00 às 22:30).',
        'horario_proibido_sexta' => 'HAE não pode ser solicitada na sexta durante o horário de aula (19:00 às 22:30).',
        'horario_proibido_sabado' => 'HAE não pode ser solicitada no sábado durante o horário de aula (07:15 às 12:50).',
        'limite_diario_segunda' => 'O limite diário de 8h foi excedido na segunda-feira.',
        'limite_diario_terca' => 'O limite diário de 8h foi excedido na terça-feira.',
        'limite_diario_quarta' => 'O limite diário de 8h foi excedido na quarta-feira.',
        'limite_diario_quinta' => 'O limite diário de 8h foi excedido na quinta-feira.',
        'limite_diario_sexta' => 'O limite diário de 8h foi excedido na sexta-feira.',
        'limite_diario_sabado' => 'O limite diário de 8h foi excedido no sábado.',
        'horario_invalido_segunda' => 'Horário inválido na segunda-feira.',
        // Adicione outros conforme necessário...
    ];

    $mensagem = $mensagens[$motivo] ?? 'Erro desconhecido.';
    echo "<div style='color: red; font-weight: bold;'>$mensagem</div>";
}

?>
</html><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Links do cabeçalho da Fatec -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/fa068c530f.js" crossorigin="anonymous"></script>
    <title>Dashboard HAE</title>
   <link rel="stylesheet" href="estilo.css">


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
            color: var(--fatec-azul);
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

    <div id="userTypeIndicator" class="user-type">
        <!-- Será preenchido via JavaScript -->
    </div>
<div class="container">
        <div id="professorContent">
            <h1>Bem vindo <?php echo $nome; ?>!</h1>
            


            <form action="../controller/salvar_solicitacao.php" method="POST" id="form-inscricao" class="needs-validation" novalidate>
    
    <!-- Bem-vindo -->
    <div class="mb-3">
      <label for="tipo" class="form-label">Qual tipo de HAE Solicitada?</label>
      <select class="form-select" id="tipo" name="tipo" required>
        <option value="" disabled selected>Escolha a HAE desejada</option>
        <option value="supervisionado">Estágio Supervisionado</option>
        <option value="Graduacao">Trabalho de Graduação</option>
        <option value="Cotas">Cotas de HAE – Inciso I ao IV</option>
        <option value="Projeto de Iniciação Científica">Projeto de Iniciação Científica</option>
        <option value="Revista Prospectus">Revista Prospectus</option>
        <option value="Divulgação dos cursos da Fatec de Itapira">Divulgação dos Cursos</option>
        <option value="Captação de alunos">Captação de Alunos</option>
      </select>
      <div class="invalid-feedback">Por favor, escolha um tipo de HAE.</div>
    </div>

    <div class="mb-3">
      <label for="curso" class="form-label">Curso:</label>
      <select class="form-select" id="curso" name="curso" required>
        <option value="" disabled selected>Escolha o curso</option>
        <option value="DSM">Desenvolvimento de Software Multiplataforma (DSM)</option>
        <option value="GPI">Gestão de Produção Industrial (GPI)</option>
        <option value="GE">Gestão Empresarial (GE)</option>
      </select>
      <div class="invalid-feedback">Por favor, escolha um curso.</div>
    </div>

    <!-- Horários Dinâmicos -->
    <div class="row">

    <br>
        <h3>Selecione o Horario Desejado</h3>
  <!-- Segunda-feira -->
  <div class="col-sm-6 mb-3">
    <label for="segunda_inicio" class="form-label"><p>Segunda-feira - Início</p></label>
    <input type="time" class="form-control" id="segunda_inicio" name="segunda_inicio">
  </div>
  <div class="col-sm-6 mb-3">
    <label for="segunda_fim" class="form-label"><p>Segunda-feira - Fim</p></label>
    <input type="time" class="form-control" id="segunda_fim" name="segunda_fim">
  </div>

  <!-- Terça-feira -->
  <div class="col-sm-6 mb-3">
    <label for="terca_inicio" class="form-label"><p>Terça-feira - Início</p></label>
    <input type="time" class="form-control" id="terca_inicio" name="terca_inicio">
  </div>
  <div class="col-sm-6 mb-3">
    <label for="terca_fim" class="form-label"><p>Terça-feira - Fim</p></label>
    <input type="time" class="form-control" id="terca_fim" name="terca_fim">
  </div>

  <!-- Quarta-feira -->
  <div class="col-sm-6 mb-3">
    <label for="quarta_inicio" class="form-label"><p>Quarta-feira - Início</p></label>
    <input type="time" class="form-control" id="quarta_inicio" name="quarta_inicio">
  </div>
  <div class="col-sm-6 mb-3">
    <label for="quarta_fim" class="form-label"><p>Quarta-feira - Fim</p></label>
    <input type="time" class="form-control" id="quarta_fim" name="quarta_fim">
  </div>

  <!-- Quinta-feira -->
  <div class="col-sm-6 mb-3">
    <label for="quinta_inicio" class="form-label"><p>Quinta-feira - Início</p></label>
    <input type="time" class="form-control" id="quinta_inicio" name="quinta_inicio">
  </div>
  <div class="col-sm-6 mb-3">
    <label for="quinta_fim" class="form-label"><p>Quinta-feira - Fim</p></label>
    <input type="time" class="form-control" id="quinta_fim" name="quinta_fim">
  </div>

  <!-- Sexta-feira -->
  <div class="col-sm-6 mb-3">
    <label for="sexta_inicio" class="form-label"><p>Sexta-feira - Início</p></label>
    <input type="time" class="form-control" id="sexta_inicio" name="sexta_inicio">
  </div>
  <div class="col-sm-6 mb-3">
    <label for="sexta_fim" class="form-label"><p>Sexta-feira - Fim</p></label>
    <input type="time" class="form-control" id="sexta_fim" name="sexta_fim">
  </div>

            <!-- Sábado -->
  <div class="col-sm-6 mb-3">
    <label for="sabado_inicio" class="form-label"><p>Sábado - Início</p></label>
    <input type="time" class="form-control" id="sabado_inicio" name="sabado_inicio">
  </div>
  <div class="col-sm-6 mb-3">
    <label for="sabado_fim" class="form-label"><p>Sábado - Fim</p></label>
    <input type="time" class="form-control" id="sabado_fim" name="sabado_fim">
  </div>
</div>


    <hr class="my-4">

    <!-- Detalhamento do Projeto -->
    <div class="mb-3">
      <label for="metas" class="form-label">Metas Relacionadas ao Projeto</label>
      <textarea class="form-control" id="metas" name="metas" rows="3" required></textarea>
    </div>

    <div class="mb-3">
      <label for="objetivos" class="form-label">Objetivos do Projeto – Detalhamento</label>
      <textarea class="form-control" id="objetivos" name="objetivos" rows="3" required></textarea>
    </div>

    <div class="mb-3">
      <label for="justificativas" class="form-label">Justificativas do Projeto</label>
      <textarea class="form-control" id="justificativas" name="justificativas" rows="3" required></textarea>
    </div>

    <div class="mb-3">
      <label for="recursos" class="form-label">Recursos Materiais e Humanos</label>
      <textarea class="form-control" id="recursos" name="recursos" rows="3" required></textarea>
    </div>

    <div class="mb-3">
      <label for="resultado" class="form-label">Resultado Esperado</label>
      <textarea class="form-control" id="resultado" name="resultado" rows="3" required></textarea>
    </div>

    <div class="mb-4">
      <label for="metodologia" class="form-label">Metodologia</label>
      <textarea class="form-control" id="metodologia" name="metodologia" rows="3" required></textarea>
    </div>

    <!-- Botão -->
    <div class="text-center">
      <button type="submit" class="btn btn-primary">Enviar Solicitação</button>
    </div>

    </div>
</div>
    </div>
    

        <!-- Scripts necessários -->
          <footer class="footer">
        <p>© 2024 Fatec Itapira - Todos os direitos reservados</p>
    </footer>
    <!-- Adicione após os outros scripts -->
</body>


</html>