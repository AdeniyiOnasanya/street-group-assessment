# Street Group Assessment – Homeowners CSV Parser

This is a Laravel, Vue (TypeScript) application that accepts a CSV upload containing a homeowner names and returns a JSON list of individual people records.

Each person record follows this schema:

- `title` (required)
- `first_name` (nullable)
- `last_name` (required)
- `initial` (nullable)

The User Interface provides:
- a upload form.
- loading, success, and error states.
- - a **JSON view**

## Tech Stack
- Laravel 12
- Vue 3 + TypeScript
- Vite
- Tailwind CSS

## Setup

### Node version 22 is required to run this application

### 1) Install dependencies
```bash
composer install
npm install 
```

### 2) Environmental file
```bash
cp .env.example .env
php artisan key:generate
```

### 3) Run application

#### Terminal 1
```bash
php artisan serve
```

#### Terminal 2
```bash
npm run dev
```




