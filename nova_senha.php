<?php
session_start();
require 'db.php';

// Se não passou pela tela de token, expulsa
if (!isset($_SESSION['reset_usuario'])) {
    header("Location: recuperar_senha.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nova_senha = $_POST['nova_senha'];
    $usuario = $_SESSION['reset_usuario'];

    // Atualiza a senha no banco
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE usuario = ?");
    $stmt->execute([$nova_senha, $usuario]);

    // Limpa a sessão de reset e manda pro login
    unset($_SESSION['reset_usuario']);
    $sucesso = "Senha atualizada com sucesso!";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }</style>
</head>
<body>
    <div class="card p-4 shadow-sm" style="max-width: 400px; width: 100%;">
        <h5 class="text-success text-center mb-4">Criar Nova Senha</h5>
        
        <?php if(isset($sucesso)): ?>
            <div class='alert alert-success text-center'><?= $sucesso ?></div>
            <a href="index.php" class="btn btn-success w-100">Ir para o Login</a>
        <?php else: ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Usuário: <?= htmlspecialchars($_SESSION['reset_usuario']) ?></label>
                    <input type="password" name="nova_senha" class="form-control" placeholder="Digite a nova senha" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Salvar Nova Senha</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>