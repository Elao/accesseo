
###########
# Install #
###########

install:
	composer update

install-lowest:
	composer update --prefer-lowest

########
# Lint #
########

lint: lint-php-cs-fixer lint-twig lint-composer

fix-php-cs-fixer:
	vendor/bin/php-cs-fixer fix

lint-php-cs-fixer:
	vendor/bin/php-cs-fixer fix --dry-run --diff

lint-composer:
	composer validate --strict

lint-twig:
	php bin/lint-twig.php templates

lint-min-php:
	@php -r "exit (PHP_MAJOR_VERSION == 7 && PHP_MINOR_VERSION == 1 ? 0 : 1);" \
		|| (echo "Please run this task using PHP 7.1" && exit 1);
	@(find src -type f -name '*.php' -exec php -l {} \; | (! grep -v "No syntax errors detected" )) && echo 'No syntax errors'

########
# Test #
########

test:
	vendor/bin/simple-phpunit
