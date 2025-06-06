<?php

class Professor {
    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=modelo", "root", "");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }

    // Cadastro completo: usuário, professor e carga horária
   public function listarSolicitacoesDoProfessor($usuario_id)
{
    $stmt = $this->pdo->prepare("
        SELECT sh.id, sh.tipo, sh.status, sh.curso, u.nome
        FROM solicitacao_hae sh
        JOIN professor p ON sh.professor_id = p.id
        JOIN usuario u ON p.usuario_id = u.id
        WHERE p.id = ?
        ORDER BY sh.id DESC
    ");
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function cadastrar($nome, $email, $senha, $rg, $diasAulas = [])
    {
        try {
            // 1. Inserir usuário
            $stmtUsuario = $this->pdo->prepare("INSERT INTO usuario (nome, email, senha, tipo_usuario) VALUES (:nome, :email, :senha, 'professor')");
            $stmtUsuario->bindParam(':nome', $nome);
            $stmtUsuario->bindParam(':email', $email);
            $stmtUsuario->bindParam(':senha', $senha); // Sugestão: use password_hash() fora daqui
            $stmtUsuario->execute();

            $usuario_id = $this->pdo->lastInsertId();

            // 2. Inserir professor (relacionado ao usuário)
            $stmtProfessor = $this->pdo->prepare("INSERT INTO professor (nome, email, senha, rg, usuario_id) VALUES (:nome, :email, :senha, :rg, :usuario_id)");
            $stmtProfessor->bindParam(':nome', $nome);
            $stmtProfessor->bindParam(':email', $email);
            $stmtProfessor->bindParam(':senha', $senha);
            $stmtProfessor->bindParam(':rg', $rg);
            $stmtProfessor->bindParam(':usuario_id', $usuario_id);
            $stmtProfessor->execute();

            $professor_id = $this->pdo->lastInsertId(); // Correto para uso em carga horária

            // 3. Inserir carga horária por dia da semana
            if (!empty($diasAulas)) {
                $stmtCarga = $this->pdo->prepare("
                    INSERT INTO cargahorariaprofessor (professor_id, dia_semana, aulas)
                    VALUES (:professor_id, :dia_semana, :aulas)
                ");
                foreach ($diasAulas as $dia => $qtdAulas) {
                    if ($qtdAulas > 0) {
                        $stmtCarga->execute([
                            ':professor_id' => $professor_id,
                            ':dia_semana' => ucfirst($dia),
                            ':aulas' => $qtdAulas
                        ]);
                    }
                }
            }

            return true;

        } catch (PDOException $e) {
            echo "Erro ao cadastrar professor: " . $e->getMessage();
            return false;
        }
    }

    public function login($email, $senha)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM professor WHERE email = :email AND senha = :senha");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha); // Mesma observação: ideal usar password_verify
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar dados completos do professor via ID de usuário
    public function buscarPorUsuarioId($usuario_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM professor WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar dados básicos do professor (nome, rg, email)
    public function buscarDadosBasicosPorUsuarioId($usuario_id)
    {
        $stmt = $this->pdo->prepare("SELECT nome, rg, email FROM professor WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obter apenas o ID do professor pelo ID do usuário
    public function getIdProfessorPorUsuarioId($usuario_id)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM professor WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['id'] : false;
    }

    // Listar todos os professores cadastrados
    public function listarProfessores()
    {
        $stmt = $this->pdo->prepare("SELECT nome, email FROM professor");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
