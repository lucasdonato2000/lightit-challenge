# Patient Registration Application

Full-stack application for patient registration built with Laravel and React, featuring async email notifications and responsive UI.

## Tech Stack

### Backend
- Laravel 11
- PostgreSQL
- Laravel Queues for async jobs
- Mailtrap for email testing

### Frontend
- React 18 with TypeScript
- Vite
- Tailwind CSS

### Infrastructure
- Docker & Docker Compose
- Multi-service architecture (Backend, Frontend, Database, Queue Worker)

## Features

- RESTful API for patient management
- Email validation (Gmail only)
- Asynchronous email notifications
- Secure image upload with encryption (ID documents contain sensitive data)
- Repository and Service Layer patterns
- Real-time form validation
- Drag & drop image upload
- Pagination
- Fully responsive design

## Prerequisites

- Docker Desktop
- Mailtrap account (free) - https://mailtrap.io/

## Setup

### 1. Clone the repository

```bash
git clone <repository-url>
cd lightit-challenge
```

### 2. Configure environment

Create `.env` file in the backend directory:

```bash
cd backend
cp .env.example .env
```

Edit `.env` and add your Mailtrap credentials:

```env
MAILTRAP_USERNAME=your_username
MAILTRAP_PASSWORD=your_password
```

### 3. Start the application

From the root directory:

```bash
docker-compose up --build
```

This will:
- Build all containers
- Set up PostgreSQL database
- Run migrations
- Start backend on port 8000
- Start frontend on port 3000
- Start queue worker

### 4. Access

- Frontend: http://localhost:3000
- Backend API: http://localhost:8000/api

## Development

### Docker Commands

```bash
# Start services
docker-compose up

# Start in background
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f

# Restart a service
docker-compose restart backend
```

### Backend Commands

```bash
# Access backend shell
docker-compose exec backend sh

# Run migrations
docker-compose exec backend php artisan migrate

# Run tests
docker-compose exec backend php artisan test

# Clear cache
docker-compose exec backend php artisan cache:clear
```

### Frontend Commands

```bash
# Access frontend shell
docker-compose exec frontend sh

# Install dependencies
docker-compose exec frontend npm install

# Build for production
docker-compose exec frontend npm run build
```

## API Endpoints

### Create Patient
```http
POST /api/patients
Content-Type: application/json

{
  "fullName": "John Doe",
  "email": "john@gmail.com",
  "phoneCountryCode": "+1",
  "phoneNumber": "5551234567",
  "documentPhoto": "base64_encoded_jpg_string"
}
```

### Get Patients (Paginated)
```http
GET /api/patients?page=1&perPage=10
```

### Get Patient by ID
```http
GET /api/patients/{id}
```

### Get Document Photo
```http
GET /api/documents/{encrypted_filename}
```

## Validation Rules

| Field | Rules |
|-------|-------|
| fullName | Required, letters and spaces only, max 255 chars |
| email | Required, unique, must be @gmail.com |
| phoneCountryCode | Required, format +XXX (1-3 digits) |
| phoneNumber | Required, numeric, 7-15 digits |
| documentPhoto | Required, JPG base64, max 5MB |

## Testing

**Important:** Always use `php artisan test` instead of `vendor/bin/phpunit` directly.

```bash
# Run all tests
docker-compose exec backend php artisan test

# Run with coverage
docker-compose exec backend php artisan test --coverage

# Run specific test
docker-compose exec backend php artisan test --filter=PatientControllerTest
```

The tests use SQLite in-memory database to avoid affecting the real database. Running `vendor/bin/phpunit` directly may use the production database.

## Project Structure

```
lightit-challenge/
├── backend/
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   ├── Repositories/
│   │   ├── Services/
│   │   ├── Jobs/
│   │   └── Mail/
│   ├── database/migrations/
│   ├── routes/api.php
│   ├── storage/app/documents/
│   └── tests/
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   ├── hooks/
│   │   ├── services/
│   │   ├── types/
│   │   └── utils/
│   └── package.json
└── docker-compose.yml
```




