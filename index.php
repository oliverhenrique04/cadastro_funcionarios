<?php
session_start();
require 'db.php';

// Se já estiver logado, manda direto para a listagem
if (isset($_SESSION['logado'])) {
    header("Location: listagem.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $senha = trim($_POST['senha']);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? AND senha = ?");
    $stmt->execute([$usuario, $senha]);
    
    if ($stmt->rowCount() > 0) {
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $user_data['usuario'];
        header("Location: listagem.php");
        exit;
    } else {
        $erro = "Usuário ou senha inválidos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Cadastro de Funcionários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { max-width: 400px; width: 100%; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border: none; padding: 2rem; }
        .btn-primary { background-color: #3b71ca; border: none; }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="text-center mb-4">
            <h4 class="text-primary fw-bold">
                <i class="fas fa-user-tie fa-2x mb-2" style="color: #2b5876;"></i><br>
                Cadastro de Funcionários
            </h4>
        </div>
        
        <?php if(isset($erro)) echo "<div class='alert alert-danger text-center'>$erro</div>"; ?>

        <form method="POST">
            <div class="input-group mb-3">
                <span class="input-group-text bg-white"><i class="fas fa-user text-secondary"></i></span>
                <input type="text" name="usuario" class="form-control" placeholder="Usuário" required>
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text bg-white"><i class="fas fa-lock text-secondary"></i></span>
                <input type="password" name="senha" class="form-control" placeholder="Senha" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3 fw-bold">Entrar</button>
            <div class="text-center border-top pt-3">
                <a href="#" class="text-secondary text-decoration-none">Esqueci minha senha</a>
            </div>
        </form>
    </div>
</body>
</html>