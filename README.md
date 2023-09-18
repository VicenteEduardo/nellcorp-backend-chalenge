Projeto de Gerenciamento de Contas Bancárias Virtuais
Este é um projeto Laravel que oferece um sistema de gerenciamento de contas bancárias virtuais com suporte para depósitos, saques, transferências e muito mais. Abaixo, você encontrará os passos para executar o projeto e os endpoints da API disponíveis.

Passos para Executar o Projeto
Configuração do Ambiente:

Certifique-se de que você possui o ambiente de desenvolvimento PHP e Composer instalados. Além disso, configure o arquivo .env com as informações de conexão do banco de dados PostgreSQL.

Instalação de Dependências:

Execute o seguinte comando para instalar as dependências do projeto:

bash
Copy code
composer install
Migrações de Banco de Dados:

Execute as migrações para criar as tabelas no banco de dados PostgreSQL:

bash
Copy code
php artisan migrate
Iniciar o Servidor de Desenvolvimento:

Inicie o servidor de desenvolvimento com o seguinte comando:

bash
Copy code
php artisan serve
Acesso ao Projeto:

Acesse o projeto em seu navegador em http://localhost:8000.

Endpoints da API
A seguir, estão os endpoints da API disponíveis no projeto:

Autenticação:

POST /login: Endpoint para fazer login no sistema.
POST /registerConta: Endpoint para registrar uma nova conta.
Operações em Contas:

POST /deposit: Endpoint para fazer um depósito em uma conta autenticada.
POST /withdraw: Endpoint para realizar um saque de uma conta autenticada.
POST /transfer: Endpoint para fazer uma transferência entre contas autenticadas.
POST /getBalance: Endpoint para obter o saldo de uma conta com base no conta_origem_id.
Histórico de Transações:

GET /historyaccount: Endpoint para obter o histórico de transações de uma conta autenticada.
Reembolso:

POST /reimbursement: Endpoint para solicitar um reembolso com base no ID da transação.
Certifique-se de autenticar-se para acessar os endpoints protegidos com auth:sanctum. 

