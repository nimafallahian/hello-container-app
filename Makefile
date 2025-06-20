.PHONY: help test lint check docker-up docker-down

help:
	@echo "Available commands:"
	@echo "  make test        - Run PHPUnit tests"
	@echo "  make lint        - Run all linters (PHP, Docker, YAML)"
	@echo "  make check       - Run all quality checks"
	@echo "  make docker-up   - Start Docker containers"
	@echo "  make docker-down - Stop Docker containers"

test:
	docker-compose exec app composer test

lint:
	docker-compose exec app composer pint
	docker-compose exec app composer phpstan
	composer docker-lint
	composer yaml-lint

check:
	docker-compose exec app composer pint-test
	docker-compose exec app composer phpstan
	docker-compose exec app composer test
	composer docker-lint
	composer yaml-lint

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down 