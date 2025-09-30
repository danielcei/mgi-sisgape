# SISGAPE

## Overview

SISGAPE is a Laravel-based project with comprehensive quality assurance tools and development workflows. This project uses Laravel 12, PHP 8.4, and a suite of quality assurance tools to ensure code quality and maintainability.

## System Requirements

### Local Requirements (for initial setup)
- PHP 8.4+ (for initial composer installation)
- Composer 2.x
- Node.js & NPM (for frontend assets)
- Docker & Docker Compose

After initial setup, all PHP and MySQL dependencies are handled by Docker containers via Laravel Sail.

## Installation

### Setting up convenient aliases

For convenience, you can create aliases to avoid typing long commands repeatedly:

#### For Mac/Linux users:

```bash
# Add these to your ~/.bashrc, ~/.zshrc, or equivalent shell configuration file
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
alias art='php artisan'
```

After adding the aliases, reload your shell configuration:

```bash
source ~/.bashrc  # or source ~/.zshrc if using Zsh
```

#### For Windows users:

Option 1: Using PowerShell profile (recommended):

1. First, check if you have a PowerShell profile:
   ```powershell
   Test-Path $PROFILE
   ```

2. If it returns `False`, create one:
   ```powershell
   New-Item -Path $PROFILE -Type File -Force
   ```

3. Open the profile in a text editor:
   ```powershell
   notepad $PROFILE
   ```

4. Add these functions to your profile:
   ```powershell
   function sail {
       if (Test-Path sail) {
           ./sail $args
       } else {
           ./vendor/bin/sail $args
       }
   }
   
   function art {
       php artisan $args
   }
   ```

5. Save and reload your profile:
   ```powershell
   . $PROFILE
   ```

Option 2: Create batch files in a directory that's in your PATH:

1. Create a file named `sail.bat` with the following content:
   ```batch
   @echo off
   if exist "sail" (
       sail %*
   ) else (
       .\vendor\bin\sail %*
   )
   ```

2. Create a file named `art.bat` with the following content:
   ```batch
   @echo off
   php artisan %*
   ```

3. Save these files in a directory that's in your PATH (e.g., `C:\Windows` or a custom bin directory)

### Configure local hosts for database access

Add an entry to your hosts file to ensure your local environment can access the MySQL database inside Docker, which is necessary for git pre-commit hooks that use IDE Helper:

```bash
# Add to /etc/hosts (Linux/Mac) or C:\Windows\System32\drivers\etc\hosts (Windows)
127.0.0.1 mysql
```

### Installation steps

1. Clone the repository:
   ```bash
   git clone git@gitlab.pdmfc.com:pdmfc/hippo2.git
   cd hippo2
   ```

2. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

3. Install Composer dependencies:

   **Reminder**: You need PHP 8.4+ installed locally:
   ```bash
   composer install
   ```

4. Start the Docker containers:

   This will start webserver, db, etc.

   ```bash
   sail up -d
   ```

5. Generate application key:
   ```bash
   art key:generate
   ```

6. Run database migrations:
   ```bash
   art migrate
   ```

### Basic Sail Commands

- Start the environment:
  ```bash
  sail up -d
  ```

- Stop the environment:
  ```bash
  sail down
  ```

- Run artisan commands:
  ```bash
  sail artisan [command]
  ```

- Run Composer commands:
  ```bash
  sail composer [command]
  ```

- Run NPM commands:
  ```bash
  sail npm [command]
  ```

- Access the application:
  http://localhost (or the port specified in your .env file)

### Development Mode

The project includes a convenient script to start all development services simultaneously:

```bash
composer dev
```

This will start:
- Laravel web server
- Queue listener
- Laravel Pail (for log monitoring)
- Vite dev server

## Code Quality Tools

Hippo 2 uses GrumPHP to maintain code quality standards. GrumPHP runs various tasks during git hooks to ensure code quality:

> **Important**: When committing code, ensure that Sail is running (`sail up -d`) since some Git hooks like IDE Helper require database access to work properly.

### Key Tools

1. **Laravel Pint**: PHP code style fixer based on PHP-CS-Fixer
   ```bash
   ./vendor/bin/pint
   ```

2. **PHPStan**: Static analysis tool
   ```bash
   ./vendor/bin/phpstan analyse
   ```

3. **Rector**: Automated code upgrades and refactoring
   ```bash
   ./vendor/bin/rector process
   ```

4. **Pest**: PHP testing framework
   ```bash
   ./vendor/bin/pest
   ```

5. **IDE Helper**: Adds docblocks to Eloquent models for better IDE support
   ```bash
   art ide-helper:models
   ```

### GrumPHP Configuration

The project uses an extensive GrumPHP configuration that includes:

- Syntax validation for PHP, JSON, and YAML files
- Code style enforcement with Laravel Pint
- Static analysis with PHPStan
- Dependency checks with Composer
- Security checks for dependencies
- Prevention of committing debugging code
- Automated model docblock generation
- Code refactoring with Rector
- Magic number detection
- Conventional Commits format enforcement
- Automated testing with Pest

## Git Workflow

The project follows specific git conventions:

1. **Branch naming convention**:
   ```
   username/task-123
   ```
   Where `username` is your user name (E.g.: jsilva), and `123` is the task ID

2. **Commit message format** (Conventional Commits):
   ```
   type(scope): description #issue-number
   ```

    - Types: feat, fix, docs, style, refactor, perf, test, build, ci, chore
    - Must include GitLab issue number
    - Learn more about [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/)

## Project Structure

The project follows the standard Laravel structure with some additions:

- `/app` - Application code
- `/app/Domain` - Domain-specific code including Quality tools
- `/database` - Database migrations, factories, and seeders
- `/tests` - Test files using Pest PHP
- `/resources` - Frontend resources and views

## Key Dependencies

- **Laravel Framework** (v12.0)
- **Filament** (v3.3) - Admin panel framework
- **Laravel Actions** (v2.9) - For action-based architecture
- **Spatie Laravel Permission** (v6.16) - For role and permission management
- **Spatie Laravel Data** (v4.13) - Data transfer objects
- **Laravel Auditing** (v14.0) - For model auditing
- **Sentry Laravel** (v4.13) - For error tracking

## Running Tests

```bash
sail pest
```
