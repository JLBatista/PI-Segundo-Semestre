<?php
class Secretaria
{
    private $pdo;

    public function __construct()
    {
        // Conexão direta sem require externo
        $host = 'localhost';
        $dbname = 'modelo'; // Altere para o nome do seu banco
        $user = 'root'; // Altere se necessário
        $pass = '';     // Altere se necessário

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    public function getPdo()
    {
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

        // Cria usuário 
        $stmt = $pdo->prepare("INSERT INTO usuario (nome, email, senha, tipo_usuario) VALUES (:nome, :email, :senha, 'secretaria')");
        $ok = $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senha
        ]);
        if (!$ok) {
            return false;
        }

        $usuario_id = $pdo->lastInsertId();

        // Cria registro na tabela secretaria
        $stmtCheck = $pdo->query("SHOW TABLES LIKE 'secretaria'");
        if ($stmtCheck && $stmtCheck->rowCount() > 0) {
            $stmt = $pdo->prepare("INSERT INTO secretaria (usuario_id) VALUES (:usuario_id)");
            $stmt->execute([':usuario_id' => $usuario_id]);
        }

        return true;
    }
    public function publicarEdital($usuario_secretaria_id, $nome_edital, $comentario, $documento)
{
    $sql = "INSERT INTO edital (usuario_secretaria_id, nome_edital, comentario, documento, data_postagem) 
            VALUES (:usuario_id, :nome_edital, :comentario, :documento, NOW())";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        ':usuario_id' => $usuario_secretaria_id,
        ':nome_edital' => $nome_edital,
        ':comentario' => $comentario,
        ':documento' => $documento
    ]);
}

    public function listarEditais()
{
    $sql = "SELECT e.*, u.nome as secretaria_nome 
            FROM edital e 
            JOIN usuario u ON e.usuario_secretaria_id = u.id 
            ORDER BY e.data_postagem DESC";
    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public function buscarEdital($id)
    {
        $sql = "SELECT * FROM edital WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function excluirEdital($id)
    {
        $sql = "DELETE FROM edital WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    public function atualizarEdital($id, $nome_edital, $comentario, $documento = null)
{
    if ($documento) {
        $sql = "UPDATE edital SET nome_edital = :nome_edital, comentario = :comentario, documento = :documento WHERE id = :id";
        $params = [
            ':nome_edital' => $nome_edital,
            ':comentario' => $comentario,
            ':documento' => $documento,
            ':id' => $id
        ];
    } else {
        $sql = "UPDATE edital SET nome_edital = :nome_edital, comentario = :comentario WHERE id = :id";
        $params = [
            ':nome_edital' => $nome_edital,
            ':comentario' => $comentario,
            ':id' => $id
        ];
    }
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($params);
}
}