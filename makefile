tests: ## Run tests
	php vendor/bin/phpunit
.PHONY: tests

lint: ## Run php-cs-fixer
	"php-cs-fixer" fix src --rules=@Symfony,-binary_operator_spaces,-single_quote,-increment_style,-standardize_increment,-yoda_style,-phpdoc_separation,-phpdoc_summary
	"php-cs-fixer" fix tests --rules=@Symfony,-binary_operator_spaces,-single_quote,-increment_style,-standardize_increment,-yoda_style,-phpdoc_separation,-phpdoc_summary
.PHONY: lint

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
.DEFAULT_GOAL := help
