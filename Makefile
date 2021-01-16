.PHONY: help
.DEFAULT_GOAL = help

## —— Symfony 🎶 ———————————————————————————————————————————————————————————————
vendor-install:	## Installation des vendors
	composer install

vendor-update:	## Mise à jour des vendors
	composer update

clean-vendor: cc-hard  ## Suppression du répertoire vendor puis un réinstall
	rm -Rf vendor
	rm composer.lock
	composer install

cc:	 ## Vider le cache
	php bin/console c:c

cc-test:	## Vider le cache de l'environnement de test
	php bin/console c:c --env=test

cc-hard: ## Supprimer le répertoire cache
	rm -fR var/cache/*

analyze: ## Fixe le coding style
	php ./vendor/bin/phpcbf
	php ./vendor/bin/phpcs

.PHONY: tests vendor
tests: vendor ## Rebuild de la BDD de test et run les tests
	make prepare-test
	php ./vendor/bin/simple-phpunit

.PHONY: prepare-dev
prepare-dev: bin ## Rebuild de la BDD de dev
	php bin/console cache:clear --env=dev
	php bin/console doctrine:database:drop --if-exists -f --env=dev
	php bin/console doctrine:database:create --env=dev
	php bin/console doctrine:schema:update -f --env=dev
	php bin/console doctrine:fixtures:load -n --env=dev

.PHONY: prepare-test
prepare-test: bin ## Rebuild de la BDD de test
	php bin/console cache:clear --env=test
	php bin/console doctrine:database:drop --if-exists -f --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:update -f --env=test
	php bin/console doctrine:fixtures:load -n --env=test

## —— Others 🛠️️ ———————————————————————————————————————————————————————————————
help: ## Liste des commandes
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
