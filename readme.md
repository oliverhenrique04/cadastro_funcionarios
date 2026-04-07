
# Sistema Integrado de Cadastro de Funcionários e Controle de Acesso

Um mini sistema web completo e responsivo para gestão de colaboradores, desenvolvido com foco em regras de negócio inteligentes, segurança e código limpo. O sistema unifica o cadastro de RH com o controle de acesso (login) da plataforma.

## Tecnologias Utilizadas

* **Front-end:** HTML5, CSS3, Bootstrap 5 (UI, Modais e Responsividade), FontAwesome (Ícones).
* **Back-end:** PHP 8+ (Puro/Vanilla).
* **Banco de Dados:** PostgreSQL.
* **Segurança e Comunicação:** PDO (PHP Data Objects), Transações SQL (Commit/Rollback) e geração de Tokens Criptográficos (`random_bytes`).

## Funcionalidades Principais

* **Arquitetura Unificada (RH + TI):** Ao cadastrar um novo funcionário, o sistema gera automaticamente uma credencial de acesso (E-mail e Senha inicial) e um Token de Recuperação Seguro.
* **Dashboard Centralizado:** Tela de listagem cruzando dados de funcionários e usuários (via `LEFT JOIN`), permitindo ao administrador gerenciar perfis e tokens em um só lugar.
* **CRUD Completo via Modais:** Edição e exclusão de dados feitas em janelas sobrepostas (Modais Bootstrap), sem necessidade de recarregar páginas extras.
* **Exclusão em Cascata Segura:** Ao excluir um funcionário, o sistema destrói permanentemente a sua credencial de login na tabela de usuários utilizando transações SQL.
* **Recuperação de Senha Segura:** Fluxo de "Esqueci minha senha" validado por Chave de Recuperação (Token) individual.
* **Paginação Real:** Listagem otimizada no banco de dados com `LIMIT` e `OFFSET`, preparada para grandes volumes de dados.

## Estrutura de Arquivos

```text
/
├── banco.sql             # Script SQL de criação das tabelas
├── db.php                # Arquivo de conexão PDO com PostgreSQL
├── index.php             # Tela de Login
├── cadastro.php          # Formulário de inclusão (Gera funcionário e usuário)
├── listagem.php          # Dashboard principal (Lista, Busca, Edita e Exclui)
├── recuperar_senha.php   # Validação do Token de Recuperação
├── nova_senha.php        # Definição de nova credencial
├── logout.php            # Destruição segura de sessão
└── README.md             # Documentação do projeto
```
Estrutura do Banco de Dados
O sistema utiliza duas tabelas que se comunicam através do E-mail do colaborador:

funcionarios: Armazena os dados de RH (Nome, Cargo, Telefone, Situação).

usuarios: Armazena as credenciais de acesso ao sistema (Login, Senha, Token de Recuperação).

Como Executar o Projeto Localmente
## 1. Pré-requisitos
Servidor Web com PHP 7.4 ou superior (recomendado PHP 8+).

Servidor PostgreSQL instalado.

Extensão PDO para PostgreSQL (pdo_pgsql) ativada no seu php.ini.

## 2. Configurando o Banco de Dados
Rode o script abaixo no seu gerenciador PostgreSQL (pgAdmin, DBeaver, etc.):
```text
SQL
CREATE DATABASE sistema_funcionarios;
\c sistema_funcionarios;

CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    usuario VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    token_recuperacao VARCHAR(100)
);

CREATE TABLE funcionarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cargo VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    telefone VARCHAR(20),
    situacao VARCHAR(10) DEFAULT 'Ativo'
);
-- Inserindo o primeiro Administrador
INSERT INTO usuarios (usuario, senha, token_recuperacao) VALUES ('admin@empresa.com', 'admin123', 'MASTER-TOKEN-999');
```
## 3. Ajustando a Conexão
Abra o arquivo db.php e insira as credenciais do seu banco de dados local:
```text
PHP
$host = 'localhost';
$dbname = 'sistema_funcionarios';
$user = 'postgres';
$password = 'SUA_SENHA_AQUI'; 
```
## 4. Acessando o Sistema
Acesse pelo navegador: http://localhost/sua_pasta/index.php.

Login Padrão: admin@empresa.com

Senha: admin123
