# Install nodejs, npm and composer
devenv: ## Install JS and PHP dependencies
	npm install
	composer install

test: ## Run JS and PHP tests
	npm test
	composer test

fix: ## Fix coding standard violations / format code
	-phpcbf

clean: ## Remove installed PHP and JS dependencies
	rm -rf vendor node_modules

update: ## Update PHP dependencies (from composer.json)
	composer update

# See http://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
.PHONY: help
.DEFAULT_GOAL := help
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | \
	    awk 'BEGIN { \
	            FS = ":.*?## "; \
	            printf "  _   _   _   _   _   _  \n / \\ / \\ / \\ / \\ / \\ / \\ \n( j | s | o | n | e | r )\n \\_/ \\_/ \\_/ \\_/ \\_/ \\_/\n"; \
	            printf "  Yes, I am a Makefile.\n\n"; \
	            printf "\033[0;33m%s\033[0m\n", "Available targets:" \
	        }; { \
	            printf "  \033[0;32m%-15s\033[0m %s\n", $$1, $$2 \
        }'
    #                                 ^^ 1. column width
