<?php

class Usuario {
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

    public function cadastrar($nome, $email, $senha, $tipo_usuario)
    {
        $stmt = $this->pdo->prepare("INSERT INTO usuario (nome, email, senha, tipo_usuario) VALUES (:nome, :email, :senha, :tipo_usuario)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':tipo_usuario', $tipo_usuario);

        return $stmt->execute();
    }

    public function login($email, $senha)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario WHERE email = :email AND senha = :senha");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarUsuario()
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, email, tipo_usuario FROM usuario");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
