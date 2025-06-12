<?php

class Diretor {
    private $pdo;

    public function getPdo() {
    return $this->pdo;
}
    public function cadastrar($nome, $email, $senha)
{
    $pdo = $this->getPdo();

    // Verifica se já existe usuário com este e-mail
    $stmt = $pdo->prepare("SELECT id FROM usuario WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        return false; // Já existe usuário com este e-mail
    }

    // Cria usuário (senha sem criptografia)
    $stmt = $pdo->prepare("INSERT INTO usuario (nome, email, senha, tipo_usuario) VALUES (:nome, :email, :senha, 'direcao')");
    $ok = $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senha
    ]);
    if (!$ok) {
        return false;
    }

    $usuario_id = $pdo->lastInsertId();

    // Cria registro na tabela diretor (se existir)
    if ($pdo->query("SHOW TABLES LIKE 'diretor'")->rowCount() > 0) {
        $stmt = $pdo->prepare("INSERT INTO diretor (usuario_id) VALUES (:usuario_id)");
        $stmt->execute([':usuario_id' => $usuario_id]);
    }

    return true;
}
    public function __construct()
    {
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=modelo", "root", "");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }

    // Listar todas as solicitações HAE com dados do professor (join)
    public function listarSolicitacoesHAE()
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                sh.id,
                sh.curso,
                sh.tipo,
                sh.data_envio,
                sh.professor_id,
                sh.status,
                sh.segunda_inicio,
                sh.segunda_fim,
                sh.terca_inicio,
                sh.terca_fim,
                sh.quarta_inicio,
                sh.quarta_fim,
                sh.quinta_inicio,
                sh.quinta_fim,
                sh.sexta_inicio,
                sh.sexta_fim,
                sh.sabado_inicio,
                sh.sabado_fim,
                sh.metas,
                sh.objetivos,
                sh.justificativas,
                sh.recursos,
                sh.resultado,
                sh.metodologia,
                p.nome,
                p.email
            FROM solicitacao_hae sh
            LEFT JOIN professor p ON sh.professor_id = p.id
            ORDER BY sh.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar detalhes de uma solicitação específica por ID
    public function buscarSolicitacaoPorId($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                sh.id,
                sh.curso,
                sh.tipo,
                sh.data_envio,
                sh.professor_id,
                sh.status,
                sh.segunda_inicio,
                sh.segunda_fim,
                sh.terca_inicio,
                sh.terca_fim,
                sh.quarta_inicio,
                sh.quarta_fim,
                sh.quinta_inicio,
                sh.quinta_fim,
                sh.sexta_inicio,
                sh.sexta_fim,
                sh.sabado_inicio,
                sh.sabado_fim,
                sh.metas,
                sh.objetivos,
                sh.justificativas,
                sh.recursos,
                sh.resultado,
                sh.metodologia,
                p.nome,
                p.email
            FROM solicitacao_hae sh
            LEFT JOIN professor p ON sh.professor_id = p.id
            WHERE sh.id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // *** NOVO: método buscado no seu exemplo (idêntico a buscarSolicitacaoPorId) ***
    public function buscarSolicitacaoComProfessor($id)
    {
        // Reutiliza a mesma query do buscarSolicitacaoPorId
        return $this->buscarSolicitacaoPorId($id);
    }

    public function buscarParecerPorSolicitacao($solicitacao_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM parecer WHERE solicitacao_id = :solicitacao_id LIMIT 1");
        $stmt->bindParam(':solicitacao_id', $solicitacao_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvarParecer($solicitacao_id, $usuario_id, $comentario, $status_final)
    {
        $parecerExistente = $this->buscarParecerPorSolicitacao($solicitacao_id);

        $data_avaliacao = date('Y-m-d H:i:s');

        if ($parecerExistente) {
            $stmt = $this->pdo->prepare("
                UPDATE parecer 
                SET comentario = :comentario, status_final = :status_final, data_avaliacao = :data_avaliacao, usuario_id = :usuario_id
                WHERE id = :id
            ");
            $stmt->bindParam(':id', $parecerExistente['id'], PDO::PARAM_INT);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO parecer (solicitacao_id, usuario_id, comentario, data_avaliacao, status_final)
                VALUES (:solicitacao_id, :usuario_id, :comentario, :data_avaliacao, :status_final)
            ");
            $stmt->bindParam(':solicitacao_id', $solicitacao_id, PDO::PARAM_INT);
        }

        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
        $stmt->bindParam(':status_final', $status_final, PDO::PARAM_STR);
        $stmt->bindParam(':data_avaliacao', $data_avaliacao, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function inserirParecer($solicitacao_id, $usuario_id, $comentario, $status_final) {
        $stmt = $this->pdo->prepare("
            INSERT INTO parecer (solicitacao_id, usuario_id, comentario, data_avaliacao, status_final)
            VALUES (:solicitacao_id, :usuario_id, :comentario, NOW(), :status_final)
        ");
        $stmt->execute([
            ':solicitacao_id' => $solicitacao_id,
            ':usuario_id' => $usuario_id,
            ':comentario' => $comentario,
            ':status_final' => $status_final
        ]);
    }
    public function listarRelatoriosHAE()
{
    $stmt = $this->pdo->prepare("
        SELECT 
            hd.id,
            hd.solicitacao_hae_id,
            hd.professor_id,
            hd.resultados,
            hd.arquivo_upload,
            hd.status_final,
            sh.tipo,
            sh.curso,
            u.nome AS nome_professor
        FROM hae_deferidas hd
        INNER JOIN solicitacao_hae sh ON sh.id = hd.solicitacao_hae_id
        INNER JOIN professor p ON p.id = hd.professor_id
        INNER JOIN usuario u ON u.id = p.usuario_id
        ORDER BY hd.id DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function atualizarStatusSolicitacao($solicitacao_id, $novo_status) {
        $stmt = $this->pdo->prepare("
            UPDATE solicitacao_hae SET status = :status WHERE id = :id
        ");
        $stmt->execute([
            ':status' => $novo_status,
            ':id' => $solicitacao_id
        ]);
    }
}
