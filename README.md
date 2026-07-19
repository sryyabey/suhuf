# Suhuf

Suhuf is an open-source Laravel platform designed to help Muslims read, reflect on, and study the Quran with notes, search, and personal insights.

## Overview

The project aims to provide a focused digital space for Quran study by combining reading, reflection, and personal knowledge capture in a single platform.

## Core Features

- Quran reading experience
- Search across content
- Personal notes and reflections
- Study-focused personal insights

## Tech Stack

- PHP 8.2
- Laravel 12
- Filament 5
- Laravel Sanctum

## Included Packages

- Filament Shield for roles and permissions
- Filament Impersonate for admin user switching
- Scramble for API documentation

## Installation

1. Install PHP and Node.js dependencies:

```bash
composer install
npm install
```

2. Create your environment file:

```bash
cp .env.example .env
```

3. Generate the application key:

```bash
php artisan key:generate
```

4. Run database migrations:

```bash
php artisan migrate --force
```

5. Build frontend assets:

```bash
npm run build
```

You can also run the full setup flow with:

```bash
composer run setup
```

## Development

Start the local development environment with:

```bash
composer run dev
```

This starts the Laravel server, queue listener, log viewer, and Vite development process together.

## Testing

Run the test suite with:

```bash
composer run test
```

## License

This project is open-sourced under the MIT license.
