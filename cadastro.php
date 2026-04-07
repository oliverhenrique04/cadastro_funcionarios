<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: index.php"); exit; }
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $cargo = $_POST['cargo'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $situacao = $_POST['situacao'];

    $stmt = $pdo->prepare("INSERT INTO funcionarios (nome, cargo, email, telefone, situacao) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$nome, $cargo, $email, $telefone, $situacao])) {
        header("Location: listagem.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Funcionários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style> 
        body { background-color: #f4f6f9; }
        .navbar-custom { background: linear-gradient(to right, #2b5876, #4e4376); } 
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-globe-americas"></i> Cadastro de Funcionários</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="listagem.php">Listagem</a></li>
                </ul>
                
                <div class="dropdown">
                    <button class="btn btn-link text-white text-decoration-none dropdown-toggle fw-bold" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        Olá, <?php echo htmlspecialchars($_SESSION['usuario']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenu">
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair do Sistema</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <h4 class="mb-3 text-primary fw-bold" style="color: #2b5876 !important;">Cadastro de Funcionários</h4>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="text-primary fw-bold" style="color: #2b5876 !important;"><i class="fas fa-user-tie"></i> Cadastro de Funcionários</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold mb-1">ID: Automático</label>
                            <input type="text" name="nome" class="form-control" placeholder="Nome" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold mb-1">Nome (Cargo)</label>
                            <select name="cargo" class="form-select">
                                <option>Administrador</option>
                                <option>Gerente</option>
                                <option>Assistente</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold mb-1">E-mail</label>
                            <input type="email" name="email" class="form-control" placeholder="E-mail">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold mb-1">E-mail (Secundário)</label>
                            <input type="email" class="form-control" placeholder="joao@dsebrosc.com" disabled>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="fw-bold mb-1">Telefone</label>
                            <input type="text" name="telefone" class="form-control" placeholder="Telefone">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold d-block mb-2">Situação</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="situacao" value="Ativo" checked>
                                <label class="form-check-label">Ativo</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="situacao" value="Inativo">
                                <label class="form-check-label">Inativo</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-center border-top pt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-bold" style="background-color: #3b71ca;">Salvar</button>
                        <button type="reset" class="btn btn-light border px-4">Limpar</button>
                        <a href="listagem.php" class="btn btn-light border px-4">Voltar</a>
                        <a href="#" class="btn btn-light border px-4">Fechar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>