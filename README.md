# BublFizz Project Structure

**Copyright EXOR Group ltd 2025**  
**Version 1.0.0.0**  
**BublFiz Social**  
**Description: Project folder structure and setup guide**  
 

## Folder Structure

```
bublFizz/
├── frontend/                 # Vue 3 + PrimeVue application
│   ├── src/
│   │   ├── components/      # Reusable Vue components
│   │   ├── views/          # Page-level components
│   │   ├── stores/         # Pinia state management
│   │   ├── router/         # Vue Router configuration
│   │   ├── services/       # API service layer
│   │   └── assets/         # Static assets (images, styles)
│   ├── public/             # Public assets
│   ├── package.json        # Node dependencies
│   └── vite.config.js      # Vite configuration
│
├── backend/                 # PHP API and server logic
│   ├── api/                # API endpoints
│   │   ├── auth/          # Authentication endpoints
│   │   ├── bubls/         # Content management
│   │   ├── pages/         # Page management
│   │   └── admin/         # Admin functionality
│   ├── config/            # Configuration files
│   │   ├── database.php   # Database configuration
│   │   └── app.php        # Application settings
│   ├── classes/           # Core PHP classes
│   │   ├── models/        # Data models
│   │   ├── controllers/   # Request handlers
│   │   └── services/      # Business logic
│   ├── middleware/        # Request middleware
│   ├── utils/             # Utility functions
│   └── vendor/            # Composer dependencies
│
├── database/               # Database management
│   ├── migrations/        # Schema migration files
│   ├── rollbacks/         # Migration rollback files
│   ├── seeders/           # Test data seeders
│   ├── migrate.php        # Migration runner
│   └── schema.sql         # Complete schema backup
│
├── docs/                   # Project documentation
│   ├── api/               # API documentation
│   ├── technical/         # Technical specifications
│   └── user/              # User guides
│
├── tests/                  # Test files
│   ├── frontend/          # Vue component tests
│   └── backend/           # PHP unit tests
│
└── deployment/             # Deployment scripts and configs
    ├── production/        # Production deployment
    └── staging/           # Staging environment
```

## Setup Instructions

### 1. Create Folder Structure
Run this in your project root:

```bash
# Main directories
mkdir -p frontend/src/{components,views,stores,router,services,assets}
mkdir -p frontend/public
mkdir -p backend/{api/{auth,bubls,pages,admin},config,classes/{models,controllers,services},middleware,utils}
mkdir -p database/{migrations,rollbacks,seeders}
mkdir -p docs/{api,technical,user}
mkdir -p tests/{frontend,backend}
mkdir -p deployment/{production,staging}
```

### 2. Database Setup
1. Create database in XAMPP/phpMyAdmin
2. Update `/backend/config/database.php` with your credentials
3. Run migrations: `php database/migrate.php run`

### 3. Frontend Setup
```bash
cd frontend
npm init vue@latest .
npm install primevue bootstrap
```

### 4. Backend Setup
```bash
cd backend
composer init
composer require monolog/monolog
```

## Development Workflow

### Local Development
- **Frontend**: `npm run dev` (runs on :3000)
- **Backend**: XAMPP Apache (runs on :80)
- **Database**: XAMPP MySQL

### Migration Commands
```bash
# Run all pending migrations
php database/migrate.php run

# Rollback last migration
php database/migrate.php rollback

# Check migration status
php database/migrate.php status
```

## Environment Configuration

Create `.env` file in project root:
```
DB_HOST=localhost
DB_NAME=bublFizz
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4

JWT_SECRET=your-secret-key
API_BASE_URL=http://localhost/bublFizz/backend/api
```

## Next Steps

1. ✅ Create folder structure
2. ✅ Setup migration system
3. ⏳ Define database schema (separate chat)
4. ⏳ Initialize Vue 3 frontend
5. ⏳ Setup PHP API routing
6. ⏳ Configure authentication system