# Symfony E-commerce Application

A simple e-commerce application built with Symfony 6.4, featuring product management and API endpoints.

## Features Implemented

- **Product Management**
  - Product listing with pagination and sorting
  - Product details view
  - Product creation form
  - Category filtering
  
- **API Endpoints**
  - `GET /api/products`: List all products with pagination
  - `GET /api/products/{id}`: Get single product details
  - Swagger documentation at `/api/doc` or `api/doc.json`

- **Technical Features**
  - Responsive design using Bootstrap
  - Form validation
  - Unit and functional tests
  - phpstan
  - API documentation with Swagger/OpenAPI

## Setup Instructions

1. Clone the repository
```bash
git clone <repository-url>
cd <project-directory>
```

2. Install dependencies
```bash
composer install
```

3. Configure the database in `.env`
```bash
DATABASE_URL="mysql://<user>:<password>@127.0.0.1:3306/symfonyTest?serverVersion=8.0.32&charset=utf8mb4"
```

4. Create database and run migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Start the Symfony server
```bash
symfony server:start
```
The application will be available at `http://localhost:8000`

## Running Tests

1. Configure test database in `.env.test`
```bash
DATABASE_URL="mysql://root:root@127.0.0.1:3306/symfonyTest_test?serverVersion=8.0.32&charset=utf8mb4"
```

2. Create test database and run migrations
```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
```

3. Run tests
```bash
php bin/phpunit
```

4. Run PHPStan for static analysis
```bash
composer phpstan
```

5. Run PHP CS Fixer for code formatting
```bash
php vendor/bin/php-cs-fixer fix
```

## Design Decisions

1. **UUID for Product IDs**
   - Used UUIDs instead of auto-increment IDs for better scalability and security

2. **Category Management**
   - Implemented as a fixed list for simplicity
   - Could be extended to a separate entity if more category features are needed

3. **API Design**
   - RESTful API with JSON responses
   - Included pagination metadata
   - Used serialization groups for controlled data exposure

4. **Frontend**
   - Bootstrap for responsive design
   - Simple and clean interface
   - Sortable columns in product list
   - Category filter dropdown

## Future Improvements

Given more time, these features could be added:

1. **Authentication & Authorization**
   - User management
   - Admin interface for product management
   - API authentication

2. **Enhanced Product Features**
   - Multiple product images
   - Stock management
   - Product variants

3. **Category Management**
   - Dynamic category management
   - Hierarchical categories
   - Category CRUD operations

4. **Testing**
   - More comprehensive test coverage
   - API test scenarios
   - Performance testing

5. **Frontend Enhancements**
   - Advanced filtering
   - Search functionality
   - Cart functionality
   - Checkout process

## Technical Requirements

- PHP 8.1 or higher
- MySQL 8.0 or higher
- Composer
- Symfony CLI (optional, for local development)