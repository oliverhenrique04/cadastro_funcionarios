<?php
session_start();
if (!isset($_SESSION['logado'])) { header("Location: index.php"); exit; }
require 'db.php';

// =======================================================
// LÓGICA DE PROCESSAMENTO (EDITAR E EXCLUIR)
// =======================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao'])) {
    
    // --- 1. EDITAR FUNCIONÁRIO E ACESSO ---
    if ($_POST['acao'] == 'editar_funcionario') {
        $id_editar = (int)$_POST['id_funcionario'];
        $nome = trim($_POST['edit_nome']);
        $cargo = trim($_POST['edit_cargo']);
        $email = trim($_POST['edit_email']);
        $telefone = trim($_POST['edit_telefone']);
        $situacao = trim($_POST['edit_situacao']);
        $nova_senha = trim($_POST['edit_senha']);
        $email_antigo = trim($_POST['old_email']);

        try {
            $pdo->beginTransaction();

            // Atualiza os dados do funcionário
            $stmt = $pdo->prepare("UPDATE funcionarios SET nome = ?, cargo = ?, email = ?, telefone = ?, situacao = ? WHERE id = ?");
            $stmt->execute([$nome, $cargo, $email, $telefone, $situacao, $id_editar]);

            // Atualiza o usuário de login
            if (!empty($nova_senha)) {
                // Atualiza email (login) e a senha
                $stmt_u = $pdo->prepare("UPDATE usuarios SET usuario = ?, senha = ? WHERE usuario = ?");
                $stmt_u->execute([$email, $nova_senha, $email_antigo]);
            } else if ($email !== $email_antigo) {
                // Se não digitou senha, mas mudou o e-mail, atualiza só o login
                $stmt_u = $pdo->prepare("UPDATE usuarios SET usuario = ? WHERE usuario = ?");
                $stmt_u->execute([$email, $email_antigo]);
            }

            $pdo->commit();
            $mensagem_sucesso = "Funcionário atualizado com sucesso!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $mensagem_erro = "Erro ao atualizar. O e-mail pode já estar em uso por outra conta.";
        }
    }

    // --- 2. EXCLUIR FUNCIONÁRIO E ACESSO ---
    if ($_POST['acao'] == 'excluir_funcionario') {
        $id_excluir = (int)$_POST['id_funcionario'];
        $email_excluir = trim($_POST['email_funcionario']);

        try {
            $pdo->beginTransaction();
            
            // Exclui o login associado
            if (!empty($email_excluir)) {
                $stmt_user = $pdo->prepare("DELETE FROM usuarios WHERE usuario = ?");
                $stmt_user->execute([$email_excluir]);
            }

            // Exclui o funcionário
            $stmt_func = $pdo->prepare("DELETE FROM funcionarios WHERE id = ?");
            $stmt_func->execute([$id_excluir]);

            $pdo->commit();
            $mensagem_sucesso = "Funcionário e acesso removidos permanentemente.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $mensagem_erro = "Erro ao excluir: " . $e->getMessage();
        }
    }
}

// =======================================================
// CONFIGURAÇÕES DE BUSCA E PAGINAÇÃO
// =======================================================
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$registros_por_pagina = 5;
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;
$params = [];

// Conta o total de registros (para paginação)
$sql_count = "SELECT COUNT(*) FROM funcionarios f";
if ($busca) {
    $sql_count .= " WHERE f.nome ILIKE ?";
    $params[] = "%$busca%";
}
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_registros = $stmt_count->fetchColumn();
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Busca os funcionários cruzando com a tabela de usuários (LEFT JOIN) para pegar o Token
$sql_data = "SELECT f.*, u.token_recuperacao FROM funcionarios f 
             LEFT JOIN usuarios u ON f.email = u.usuario";
if ($busca) {
    $sql_data .= " WHERE f.nome ILIKE ?";
}
$sql_data .= " ORDER BY f.id ASC LIMIT $registros_por_pagina OFFSET $offset";
$stmt_data = $pdo->prepare($sql_data);
$stmt_data->execute($params);
$funcionarios = $stmt_data->fetchAll(PDO::FETCH_ASSOC);
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
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Início</a></li>
                    <li class="nav-item"><a class="nav-link active fw-bold border-bottom" href="listagem.php">Listagem</a></li>
                </ul>
                <div class="dropdown">
                    <button class="btn btn-link text-white text-decoration-none dropdown-toggle fw-bold" data-bs-toggle="dropdown">
                        Olá, <?= htmlspecialchars($_SESSION['usuario']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair do Sistema</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <h4 class="mb-4 text-primary fw-bold" style="color: #2b5876 !important;">Listagem de Funcionários</h4>
        
        <?php 
        // Mensagem vinda do cadastro.php
        if(isset($_SESSION['mensagem'])){
            echo "<div class='alert alert-success alert-dismissible fade show'><i class='fas fa-check-circle'></i> {$_SESSION['mensagem']} <button class='btn-close' data-bs-dismiss='alert'></button></div>";
            unset($_SESSION['mensagem']);
        }
        if(isset($mensagem_sucesso)) echo "<div class='alert alert-success alert-dismissible fade show'><i class='fas fa-check-circle'></i> $mensagem_sucesso <button class='btn-close' data-bs-dismiss='alert'></button></div>"; 
        if(isset($mensagem_erro)) echo "<div class='alert alert-danger alert-dismissible fade show'><i class='fas fa-exclamation-triangle'></i> $mensagem_erro <button class='btn-close' data-bs-dismiss='alert'></button></div>"; 
        ?>

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
                                <th>E-mail (Login)</th>
                                <th>Situação</th>
                                <th>Token (Recuperar Senha)</th>
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
                                    <td>
                                        <code class="bg-light px-2 py-1 border rounded text-dark">
                                            <?= !empty($func['token_recuperacao']) ? htmlspecialchars($func['token_recuperacao']) : 'Sem Acesso' ?>
                                        </code>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <button class="btn btn-sm btn-light border text-primary btn-editar" 
                                                title="Editar Funcionário"
                                                data-bs-toggle="modal" data-bs-target="#modalEditar"
                                                data-id="<?= $func['id'] ?>"
                                                data-nome="<?= htmlspecialchars($func['nome']) ?>"
                                                data-cargo="<?= htmlspecialchars($func['cargo']) ?>"
                                                data-email="<?= htmlspecialchars($func['email']) ?>"
                                                data-telefone="<?= htmlspecialchars($func['telefone']) ?>"
                                                data-situacao="<?= $func['situacao'] ?>">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Excluir <?= htmlspecialchars($func['nome']) ?> do sistema e revogar seu acesso?\n\nEsta ação é irreversível.');">
                                            <input type="hidden" name="acao" value="excluir_funcionario">
                                            <input type="hidden" name="id_funcionario" value="<?= $func['id'] ?>">
                                            <input type="hidden" name="email_funcionario" value="<?= htmlspecialchars($func['email']) ?>">
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Excluir Funcionário">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center py-3">Nenhum funcionário encontrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_paginas > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($pagina_atual <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?pagina=<?= $pagina_atual - 1 ?>&busca=<?= urlencode($busca) ?>">Anterior</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item <?= ($i == $pagina_atual) ? 'active' : '' ?>">
                                <a class="page-link <?= ($i != $pagina_atual) ? 'text-dark' : '' ?>" href="?pagina=<?= $i ?>&busca=<?= urlencode($busca) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($pagina_atual >= $total_paginas) ? 'disabled' : '' ?>">
                            <a class="page-link text-dark" href="?pagina=<?= $pagina_atual + 1 ?>&busca=<?= urlencode($busca) ?>">Próximo >></a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
                
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditar" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST">
            <div class="modal-header">
              <h5 class="modal-title text-primary fw-bold"><i class="fas fa-user-edit"></i> Editar Funcionário</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
              <input type="hidden" name="acao" value="editar_funcionario">
              <input type="hidden" name="id_funcionario" id="edit_id">
              <input type="hidden" name="old_email" id="old_email">
              
              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label fw-bold">Nome</label>
                      <input type="text" name="edit_nome" id="edit_nome" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-bold">Cargo</label>
                      <select name="edit_cargo" id="edit_cargo" class="form-select">
                          <option>Administrador</option>
                          <option>Gerente</option>
                          <option>Assistente</option>
                      </select>
                  </div>
              </div>

              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label fw-bold">E-mail (Login)</label>
                      <input type="email" name="edit_email" id="edit_email" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-bold">Telefone</label>
                      <input type="text" name="edit_telefone" id="edit_telefone" class="form-control">
                  </div>
              </div>

              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label fw-bold d-block">Situação</label>
                      <div class="form-check form-check-inline mt-2">
                          <input class="form-check-input" type="radio" name="edit_situacao" id="sit_ativo" value="Ativo">
                          <label class="form-check-label">Ativo</label>
                      </div>
                      <div class="form-check form-check-inline mt-2">
                          <input class="form-check-input" type="radio" name="edit_situacao" id="sit_inativo" value="Inativo">
                          <label class="form-check-label">Inativo</label>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-bold text-danger">Nova Senha de Acesso</label>
                      <input type="password" name="edit_senha" class="form-control" placeholder="Deixe em branco para não alterar">
                  </div>
              </div>
            </div>
            <div class="modal-footer bg-light">
              <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary fw-bold" style="background-color: #3b71ca;">Salvar Alterações</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preenche o Modal de Edição com os dados da linha clicada
        document.addEventListener('DOMContentLoaded', function() {
            var botoesEditar = document.querySelectorAll('.btn-editar');
            botoesEditar.forEach(function(botao) {
                botao.addEventListener('click', function() {
                    document.getElementById('edit_id').value = this.getAttribute('data-id');
                    document.getElementById('edit_nome').value = this.getAttribute('data-nome');
                    document.getElementById('edit_cargo').value = this.getAttribute('data-cargo');
                    document.getElementById('edit_email').value = this.getAttribute('data-email');
                    document.getElementById('old_email').value = this.getAttribute('data-email');
                    document.getElementById('edit_telefone').value = this.getAttribute('data-telefone');
                    
                    var situacao = this.getAttribute('data-situacao');
                    if (situacao === 'Ativo') { document.getElementById('sit_ativo').checked = true; }
                    else { document.getElementById('sit_inativo').checked = true; }
                });
            });
        });
    </script>
</body>
</html>