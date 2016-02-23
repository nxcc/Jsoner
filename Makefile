# Install nodejs, npm and composer
devenv:
	npm install
	composer install

test:
	npm test
	composer test

fix:
	-phpcbf

clean:
	rm -rf vendor node_modules
	rm -f composer.lock
