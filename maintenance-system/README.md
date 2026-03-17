# ⚙️ Sistema Integrado de Gestão Industrial & Manutenção (SIGI)

Este projeto é uma plataforma completa para o controlo de ativos industriais, integrando o ciclo de vida das máquinas, operações de manutenção, gestão de stock e fluxos de aprovação hierárquicos.

---

## 🚀 Visão Geral do Sistema

O sistema foi desenhado para centralizar quatro áreas críticas da operação industrial:

### 1. Registo de Máquinas (Ativos)
* **Catálogo Técnico:** Registo de marca, modelo, número de série e localização.
* **Monitorização de Estado:** Indicadores visuais de máquinas em operação, paradas ou em manutenção crítica.

### 2. Gestão de Manutenção
* **Submissão de Avarias:** Interface para operadores reportarem falhas em tempo real.
* **Ordens de Trabalho:** Fluxo completo desde a abertura do chamado até à validação técnica final.
* **Histórico:** Registo permanente de intervenções por máquina para análise de fiabilidade.

### 3. Gestão de Stock e Peças
* **Inventário:** Controle de quantidades, categorias e armazéns.
* **Consumo Vinculado:** Registo automático de que peça foi utilizada em qual manutenção.
* **Alertas:** Notificações de rutura de stock para peças críticas.

### 4. Requisições e Permissões (RBAC)
* **Níveis de Acesso:** Implementação rigorosa via Spatie (Super Admin, Técnico, Operador).
* **Fluxo de Aprovação:** Requisições de material que exigem validação de superiores antes da saída de stock.

---

## 🛠️ Guia de Instalação (Máquina Local)

Siga rigorosamente a ordem abaixo para configurar o ambiente de desenvolvimento:

### 1. Preparação de Ficheiros
```bash
# Instalar dependências do Backend (PHP)
composer install

# Criar ficheiro de configuração local
cp .env.example .env

# Gerar chave única de segurança
php artisan key:generate


# Executar migrations e o seeder de permissões específico
php artisan db:seed --class=RoleAndPermissionSeeder

#
$ php artisan db:seed --class=PermissoesModulosSeeder

php artisan db:seed --class=RoleAndPermissionSeeder
php artisan permission:cache-reset


1. A Solução Rápida (Criar os tanques no PC B)
Sempre que mudares de computador, como a base de dados começa vazia, precisas de correr as migrações e criar os tanques novamente:

No computador novo, corre: php artisan migrate

Cria os tanques via terminal: php artisan tinker

Cola o comando:

PHP
App\Models\Tank::create(['nome' => 'Tanque da Fem', 'capacidade' => 43251, 'stock_atual' => 43251]);
App\Models\Tank::create(['nome' => 'Bymoze', 'capacidade' => 20000, 'stock_atual' => 20000]);
App\Models\Tank::create(['nome' => 'Bomba Móvel', 'capacidade' => 1000, 'stock_atual' => 1000]);
App\Models\Tank::create(['nome' => 'Nitro', 'capacidade' => 24799, 'stock_atual' => 24799]);


2. A Solução Profissional (Seeds)
Para não teres de escrever isto sempre, podes criar um "Semeador" (Seeder). Assim, em qualquer computador novo, basta um comando para os tanques aparecerem.

Cria o Seeder: php artisan make:seeder TankSeeder

Abre database/seeders/TankSeeder.php e coloca isto no método run:

PHP
public function run(): void
{
    \App\Models\Tank::updateOrCreate(['nome' => 'Tanque da Fem'], ['capacidade' => 43251, 'stock_atual' => 0]);
    \App\Models\Tank::updateOrCreate(['nome' => 'Bymoze'], ['capacidade' => 20000, 'stock_atual' => 0]);
    \App\Models\Tank::updateOrCreate(['nome' => 'Bomba Móvel'], ['capacidade' => 1000, 'stock_atual' => 0]);
    \App\Models\Tank::updateOrCreate(['nome' => 'Nitro'], ['capacidade' => 24799, 'stock_atual' => 0]);
}
No outro computador, basta correr:

Bash
php artisan db:seed --class=TankSeeder


# Instalar dependências do Node.js
npm install

# Compilar assets (CSS/JS) para o navegador
npm run build

# Iniciar o servidor local
php artisan serve






# Em caso de usar MYSQL, ajustar o .env conforme abaixo:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_teu_banco
DB_USERNAME=root
DB_PASSWORD=


🔐 Credenciais de Acesso Rápido
Após a instalação, utilize os seguintes dados para aceder ao sistema:

URL: http://127.0.0.1:8000

Email: admin@sistema.com

Password: password

Nota: Por questões de segurança, altere a palavra-passe após o primeiro acesso no painel de perfil.

🏗️ Pilha Tecnológica
Framework: Laravel 10+

Permissões: Spatie Laravel Permission

Frontend: Tailwind CSS & Blade Components

Base de Dados: MySQL / PostgreSQL / SQLite