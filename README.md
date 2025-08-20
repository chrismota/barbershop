# 💈 Barbershop API - Laravel 12  

API de gerenciamento de barbearia desenvolvida em **Laravel 12**, com autenticação via **Sanctum**, sistema de **agendamento de horários**, **envio de e-mails automáticos para administradores**, **filas de processamento**, documentação com **Apidog** e banco de dados **MySQL**.  

---

## 🚀 Tecnologias Utilizadas  

- ⚡ [Laravel 12](https://laravel.com)  
- 🛢️ [MySQL](https://www.mysql.com/)  
- 🔑 [Laravel Sanctum](https://laravel.com/docs/12.x/sanctum)  
- 📩 [Mail (SMTP)](https://laravel.com/docs/12.x/mail)  
- 📨 [Queues](https://laravel.com/docs/12.x/queues)  
- 📖 [Apidog](https://apidog.com/) - documentação da API  
- 🧪 [Insomnia](https://insomnia.rest/) - testes da API  

---

## 📦 Instalação do Projeto  

### 1️⃣ Clonar o repositório  
```bash
git clone https://github.com/seuusuario/barbershop.git
cd barbershop
```
### 2️⃣ Instalar dependências
```bash
composer install
npm install && npm run build
```
### 3️⃣ Configurar variáveis de ambiente

Crie o arquivo .env baseado no .env.example:
```bash
cp .env.example .env
```

### 4️⃣ Gerar chave da aplicação

```
php artisan key:generate
```

### 5️⃣ Configurar banco de dados

Edite o arquivo `.env` com as credenciais do seu MySQL:

```
DB_CONNECTION=mysql
DB_HOST=seuhost
DB_PORT=suaporta
DB_DATABASE=barbershop
DB_USERNAME=seuusername
DB_PASSWORD=suasenha
```
Crie o banco de dados no MySQL:

```
CREATE DATABASE barbershop;
```

Rodar migrations e seeders:
```bash
php artisan migrate --seed
```

### 6️⃣ Configurar serviço de e-mail

Edite o arquivo `.env` com as credenciais de e-mail:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seuemail@gmail.com
MAIL_PASSWORD=sua_senha_de_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seuemail@gmail.com
MAIL_FROM_NAME="Barbershop"
```
⚠️ Para Gmail, é necessário ativar 2FA e gerar uma senha de aplicativo neste [link](https://myaccount.google.com/apppasswords).


### 7️⃣ Rodar filas

Para envio de e-mails assíncronos, rode o worker de filas:
```bash
php artisan queue:work
```

### 8️⃣ Iniciar servidor local
```bash
php artisan serve
```

API disponível em:
👉 http://127.0.0.1:8000

## 📖 Documentação da API (Apidog)

A documentação está disponível via Apidog.

**Como visualizar:**

Abra o Apidog.

Importe o arquivo de especificação da API (`barbershop.swagger.json`) presente na raiz do projeto.

Todas as rotas estarão disponíveis com exemplos de requisição.

## ✅ Funcionalidades Implementadas

- 🔑 Autenticação com Sanctum

- 👤 Cadastro e login de clientes e administradores

- 📅 Listagem de horários livres para agendamento

- ✍️ Agendamento de horários pelo cliente (e opcionalmente pelo admin)

- 📋 Listagem de agendamentos do cliente

- 📧 Envio de e-mail automático para admins na criação/edição de agendamentos

- 📨 Processamento de e-mails via queues

- 🗃️ Migrations e seeders para popular o banco

## 🛠️ Rotas Backend com Insomnia
O projeto inclui um arquivo de exportação do Insomnia (`barbershop.yaml`) com todas as rotas organizadas.

**Como usar:**
1. Abra o Insomnia.
2. Importe o arquivo `barbershop.yaml` (na raiz do projeto).
3. Todas as rotas estarão disponíveis, incluindo:

### 🧑‍💼 Rotas de Administrador:
- CRUD de usuários administradores
- Alteração e remoção de dados de clientes
- Listagem de clientes, administradores e agendamentos
- CRUD de agendamentos (opcional)


### 🙋‍♂️ Rotas de Cliente:
- Listagem de horários disponíveis para agendamento
- CRUD do próprio perfil
- CRUD de seus agendamentos
- Listagem de todos seus agendamentos


## 👨‍💻 Autor
Christian Mota

[GitHub](https://github.com/chrismota)
