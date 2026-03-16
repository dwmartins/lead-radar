# Lead Radar

O **Lead Radar** é uma plataforma completa para gerenciamento e captura de leads, operando com um sistema de planos (SaaS) que limita o uso baseado em **buscas mensais**. O projeto é construído como uma SPA (Single Page Application) utilizando Laravel API e Vue.js.

## 🚀 Funcionalidades Principais

- **Gestão de Planos e Limites**:
  - **Plano Gratuito**: 50 buscas/mês.
  - **Plano Básico**: 500 buscas/mês.
  - **Plano Profissional**: 2.500 buscas/mês.
- **Controle de Uso**: O sistema consome "créditos de busca" a cada requisição ao Google Places, bloqueando novas ações quando o limite mensal é atingido.
- **Ranking de Leads**: Embora o limite seja por buscas, o sistema rastreia quantos leads foram efetivamente capturados para gerar rankings de desempenho.
- **Painel Administrativo**: Visão geral de usuários, planos e estatísticas do sistema.
- **Autenticação Segura**: Utilizando Laravel Sanctum com suporte a CSRF.

## 🛠 Tecnologias Utilizadas

- **Backend**: Laravel 12x
- **Frontend**: Vue.js 3 (Composition API, Pinia, Vue Router)
- **Banco de Dados**: MySQL
- **Build Tool**: Vite

## 📋 Pré-requisitos

- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL

## 🔧 Instalação

1. **Clone o repositório:**
   ```bash
   git clone <url-do-repositorio>
   cd lead-radar
   ```

2. **Instale as dependências:**
   ```bash
   # Backend
   composer install

   # Frontend
   npm install
   ```

3. **Configuração de Ambiente:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Configure as credenciais do banco de dados no arquivo `.env`.*

4. **Banco de Dados:**
   Execute as migrações e os seeders para criar a estrutura e os planos iniciais.
   ```bash
   php artisan migrate --seed
   ```

## ⚡ Como Executar

Para iniciar o ambiente de desenvolvimento, execute os comandos em terminais separados:

**Backend (API):**
```bash
php artisan serve
```

**Frontend (Vite Server):**
```bash
npm run dev
```

**Ou apenas um comando**
```bash
composer run dev
```