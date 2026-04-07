<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: index.php"); exit; }
require 'db.php';

// Sistema de busca
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
if ($busca) {
    $stmt = $pdo->prepare("SELECT * FROM funcionarios WHERE nome ILIKE ? ORDER BY id ASC");
    $stmt->execute(["%$busca%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM funcionarios ORDER BY id ASC");
}
$funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Listagem - Funcionários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style> 
        body { background-color: #f4f6f9; }
        .navbar-custom { background: linear-gradient(to right, #2b5876, #4e4376); } 
        .table-light th { color: #2b5876; }
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
                    <li class="nav-item"><a class="nav-link active fw-bold border-bottom" href="listagem.php">Listagem</a></li>
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
        <h4 class="mb-4 text-primary fw-bold" style="color: #2b5876 !important;">Listagem de Funcionários</h4>
        
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="GET" class="d-flex mb-4">
                    <div class="input-group me-2">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-secondary"></i></span>
                        <input type="text" name="busca" class="form-control border-start-0" placeholder="Buscar funcionário..." value="<?= htmlspecialchars($busca) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary me-2 px-4 fw-bold" style="background-color: #3b71ca;">Pesquisar</button>
                    <a href="cadastro.php" class="btn btn-primary text-nowrap px-4 fw-bold" style="background-color: #3b71ca;">Novo Funcionário</a>
                </form>

                <div class="table-responsive border rounded">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Nome</th>
                                <th>Cargo</th>
                                <th>E-mail</th>
                                <th>Situação</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($funcionarios) > 0): ?>
                                <?php foreach($funcionarios as $func): ?>
                                <tr>
                                    <td class="ps-3 fw-bold"><?= $func['id'] ?>.</td>
                                    <td class="text-primary"><?= htmlspecialchars($func['nome']) ?></td>
                                    <td><?= htmlspecialchars($func['cargo']) ?></td>
                                    <td><i><?= htmlspecialchars($func['email']) ?></i></td>
                                    <td>
                                        <?php if($func['situacao'] == 'Ativo'): ?>
                                            <span class="badge bg-success px-2 py-1">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary px-2 py-1">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-light border text-primary"><i class="fas fa-pen"></i></button>
                                        <button class="btn btn-sm btn-light border text-info"><i class="fas fa-envelope"></i></button>
                                        <button class="btn btn-sm btn-light border text-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-3">Nenhum funcionário encontrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link text-dark" href="#">2</a></li>
                        <li class="page-item"><a class="page-link text-dark" href="#">3</a></li>
                        <li class="page-item"><a class="page-link text-dark" href="#">Próximo >></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>