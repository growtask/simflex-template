.PHONY: cs-fix phpstan

cs-fix:
	composer dump-autoload --no-interaction --no-scripts
	./vendor/bin/php-cs-fixer fix

phpstan:
	./vendor/bin/phpstan analyse --memory-limit=1G
