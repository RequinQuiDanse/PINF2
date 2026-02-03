# Harmonie & Sens - Makefile
# Commandes utilitaires pour le développement

.PHONY: help install start stop db-start db-stop db-reset migrate fresh cache test

# Couleurs
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
RESET  := $(shell tput -Txterm sgr0)

## Aide
help: ## Affiche cette aide
	@echo ''
	@echo '${GREEN}Harmonie & Sens - Commandes disponibles${RESET}'
	@echo ''
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  ${YELLOW}%-15s${RESET} %s\n", $$1, $$2}' $(MAKEFILE_LIST)
	@echo ''

## Installation
install: ## Installe les dépendances et configure le projet
	@echo "${GREEN}Installation des dépendances...${RESET}"
	composer install
	@if [ ! -f .env.local ]; then \
		echo "${YELLOW}Création de .env.local...${RESET}"; \
		cp .env.example .env.local; \
		echo "${YELLOW}⚠️  Pensez à éditer .env.local avec vos valeurs${RESET}"; \
	fi
	@echo "${GREEN}✅ Installation terminée${RESET}"

## Serveur
start: ## Démarre le serveur de développement (Symfony CLI)
	symfony server:start

start-php: ## Démarre le serveur de développement (PHP built-in)
	php -S localhost:8000 -t public/

## Docker / Base de données
db-start: ## Démarre la base de données (Docker)
	docker compose up -d

db-stop: ## Arrête la base de données (Docker)
	docker compose down

db-logs: ## Affiche les logs de la base de données
	docker compose logs -f database

db-reset: ## Supprime et recrée la base de données
	php bin/console doctrine:database:drop --force --if-exists
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate --no-interaction

## Migrations
migrate: ## Exécute les migrations en attente
	php bin/console doctrine:migrations:migrate --no-interaction

migration: ## Crée une nouvelle migration
	php bin/console make:migration

## Setup complet
fresh: db-start ## Installation fraîche complète (DB + migrations + données initiales)
	@echo "${GREEN}Attente du démarrage de PostgreSQL...${RESET}"
	@sleep 3
	php bin/console doctrine:database:create --if-not-exists
	php bin/console doctrine:migrations:migrate --no-interaction
	php bin/console app:init-services || true
	@echo "${GREEN}✅ Setup complet terminé${RESET}"

## Cache
cache: ## Vide le cache
	php bin/console cache:clear

## Commandes utilitaires
admin: ## Crée un compte administrateur
	php bin/console app:create-admin

init-services: ## Initialise les services par défaut
	php bin/console app:init-services

## Qualité de code
lint: ## Vérifie la syntaxe des templates Twig
	php bin/console lint:twig templates/

lint-yaml: ## Vérifie la syntaxe des fichiers YAML
	php bin/console lint:yaml config/
