<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario']);
    $token = trim($_POST['token']);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? AND token_recuperacao = ?");
    $stmt->execute([$usuario, $token]);
    
    if ($stmt->rowCount() > 0) {
        // Token válido! Libera o acesso para trocar a senha
        $_SESSION['reset_usuario'] = $usuario;
        header("Location: nova_senha.php");
        exit;
    } else {
        $erro = "Usuário ou Chave de Recuperação inválidos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }</style>
</head>
<body>
    <div class="card p-4 shadow-sm" style="max-width: 400px; width: 100%;">
        <h5 class="text-primary text-center mb-4"><i class="fas fa-key"></i> Recuperação de Senha</h5>
        <?php if(isset($erro)) echo "<div class='alert alert-danger'>$erro</div>"; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Seu Usuário</label>
                <input type="text" name="usuario" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Chave de Recuperação (Token)</label>
                <input type="text" name="token" class="form-control" placeholder="Ex: REC-ADMIN-1234" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">Validar Chave</button>
            <a href="index.php" class="btn btn-light w-100 border">Voltar ao Login</a>
        </form>
    </div>
</body>
</html>