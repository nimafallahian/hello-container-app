# Order Management System

A simple Laravel-based order management system with payment processing notifications.

## Quick Start

1. **Clone and setup**
   ```bash
   git clone git@github.com:nimafallahian/hello-container-app.git
   cd hello-container-app
   cp .env.example .env
   ```

2. **Start with Docker**
   ```bash
   make docker-up
   ```

3. **Generate app key and run migrations**
   ```bash
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate
   ```

That's it! The app runs at `http://localhost:8000`

## What it does

- Create and manage orders
- Handle payment notifications
- Send payment request emails
- API endpoints for order operations

## Useful commands

```bash
make test        # Run tests
make lint        # Check code style
make check       # Run all quality checks
```

## API Documentation

Visit `/api-docs` when the app is running to see the full API documentation.

## Testing

The project includes comprehensive tests for orders, payments, and integrations. Just run `make test` to execute them all.
