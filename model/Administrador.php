
<?php
class Administrador
{
    private $pdo;

    public function __construct()
    {
        // Ajuste os dados conforme seu ambiente
        $host = 'localhost';
        $dbname = 'modelo'; // Altere para o nome do seu banco
        $user = 'root';
        $pass = '';

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

    // Editar usuário (nome, email, tipo_usuario, senha opcional)
     public function editarUsuario($id, $nome, $email, $tipo_usuario, $senha = null)
    {
        if ($senha !== null && $senha !== '') {
            $sql = "UPDATE usuario SET nome = :nome, email = :email, tipo_usuario = :tipo_usuario, senha = :senha WHERE id = :id";
            $params = [
                ':nome' => $nome,
                ':email' => $email,
                ':tipo_usuario' => $tipo_usuario,
                ':senha' => $senha,
                ':id' => $id
            ];
        } else {
            $sql = "UPDATE usuario SET nome = :nome, email = :email, tipo_usuario = :tipo_usuario WHERE id = :id";
            $params = [
                ':nome' => $nome,
                ':email' => $email,
                ':tipo_usuario' => $tipo_usuario,
                ':id' => $id
            ];
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function excluirUsuario($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM usuario WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Buscar usuário por ID
    public function buscarUsuarioPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, email, tipo_usuario FROM usuario WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}