<?php
session_start();

include '../model/Usuario.php';
include '../model/Professor.php';
include '../model/Diretor.php';
include '../model/Secretaria.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Tente primeiro logar no Usuario (administrador, professor)
    $usuario = new Usuario();
    $dados = $usuario->login($email, $senha);

    if ($dados) {
        // Usuário encontrado na tabela usuarios
        $_SESSION['id'] = $dados['id'];
        $_SESSION['nome'] = $dados['nome'];
        $_SESSION['tipo_usuario'] = $dados['tipo_usuario'];

        // Redireciona baseado no tipo
        if ($dados['tipo_usuario'] == 'administrador') {
            header("Location: ../view/admin.php");
        } elseif ($dados['tipo_usuario'] == 'professor') {
            header("Location: ../view/dashboard_professor.php");
        }
         elseif ($dados['tipo_usuario'] == 'direcao') {
            header("Location: ../view/dashboard_coordenacao.php");
        }
        elseif ($dados['tipo_usuario'] == 'secretaria') {
            header("Location: ../view/dashboard_secretaria.php");
        }
        exit;
        } else {

            $professor = new Professor();
            $dadosProfessor = $professor->login($email, $senha);

            if ($dadosProfessor) {
                // Usuário encontrado na tabela professores
                $_SESSION['id'] =  $dadosProfessor['id'];
                $_SESSION['nome'] =  $dadosProfessor['nome'];
                $_SESSION['tipo_usuario'] = 'professor'; 

                header("Location: ../view/professor.php");
                exit;
            } else {
                // Nenhum usuário encontrado
                $_SESSION['login_error'] = "Email ou senha incorretos!";
                header("Location: ../index.php");
                exit;
            }
        }
    } else {
    header("Location: ../index.php");
    exit;
}
