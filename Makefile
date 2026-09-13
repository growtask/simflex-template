.PHONY: cs-fix

cs-fix:
	composer dump-autoload --no-interaction --no-scripts
	./vendor/bin/php-cs-fixer fix
