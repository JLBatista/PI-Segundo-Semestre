<?php

class CargaHora {
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

    public function carga($dia_semana, $aulas)
    {
        $stmt = $this->pdo->prepare("INSERT INTO cargahorariaprofessor (dia_semana, aulas) VALUES (:dia_semana, :aulas)");
        $stmt->bindParam(':dia_semana', $dia_semana);
        $stmt->bindParam(':aulas', $aulas);

        return $stmt->execute();
    }
}
