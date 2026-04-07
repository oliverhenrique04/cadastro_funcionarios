# Mini Sistema de Cadastro de Funcionários

Este é um mini sistema web para gerenciamento de funcionários, desenvolvido com foco em simplicidade, responsividade e código limpo. O projeto contempla um fluxo completo de autenticação e um CRUD (Create, Read) de dados básicos de colaboradores.

## Tecnologias Utilizadas

* **Front-end:** HTML5, CSS3, Bootstrap 5 (via CDN), FontAwesome (ícones)
* **Back-end:** PHP (Puro/Vanilla)
* **Banco de Dados:** PostgreSQL
* **Comunicação de Banco:** PDO (PHP Data Objects) para maior segurança contra SQL Injection.

## Funcionalidades Implementadas

* **Autenticação de Usuário:** Tela de login validando credenciais no banco de dados e controle de acesso via Sessão (PHP Sessions).
* **Logout Seguro:** Opção na barra de navegação para encerrar a sessão de forma segura.
* **Cadastro de Funcionários:** Formulário para inclusão de novos colaboradores com campos de Nome, Cargo, E-mail, Telefone e Situação (Ativo/Inativo).
* **Listagem de Funcionários:** Tabela de exibição de todos os cadastros.
* **Busca Simples:** Filtro de pesquisa pelo nome do funcionário diretamente na tela de listagem.
* **Interface Responsiva:** O layout se adapta perfeitamente a dispositivos móveis e desktops graças ao Bootstrap 5.

## Estrutura de Arquivos

```text
/
├── banco.sql       # Script SQL para criação do banco de dados e tabelas
├── db.php          # Arquivo de conexão com o banco de dados PostgreSQL
├── index.php       # Tela 1: Login
├── cadastro.php    # Tela 2: Formulário de cadastro de funcionários
├── listagem.php    # Tela 3: Tabela de listagem e pesquisa
├── logout.php      # Script para destruição de sessão e logoff
└── README.md       # Documentação do projeto
```
## Como Executar o Projeto Localmente

Pré-requisitos
Servidor Web com PHP 7.4 ou superior (XAMPP, WAMP, Laragon, ou embutido do PHP).

Servidor PostgreSQL instalado e rodando.

Extensão PDO para PostgreSQL (pdo_pgsql) ativada no php.ini.

## Passo a Passo de Instalação
Clone ou baixe o projeto
Coloque os arquivos na pasta pública do seu servidor web (ex: htdocs ou www).

## Configure o Banco de Dados
Abra o seu gerenciador do PostgreSQL (como o pgAdmin) e rode os comandos contidos no arquivo banco.sql para criar o banco de dados sistema_funcionarios e as tabelas usuarios e funcionarios.

## Ajuste a Conexão
Abra o arquivo db.php e edite as credenciais de acesso ao seu banco de dados local:

PHP
$host = 'localhost';
$dbname = 'sistema_funcionarios';
$user = 'postgres';
$password = 'SUA_SENHA_AQUI'; // Altere para a senha do seu Postgres
Inicie a Aplicação
Acesse o projeto pelo navegador: http://localhost/sua_pasta/index.php.

Credenciais de Acesso Padrão:

Usuário: Admin

Senha: admin123
