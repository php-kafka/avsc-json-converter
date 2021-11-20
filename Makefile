.PHONY: clean code-style pcov-enable pcov-disable
.DEFAULT_GOAL := code-style

PHPCS = ./vendor/bin/phpcs --extensions=php

clean:
	rm -rf ./build ./vendor

code-style: pcov-disable
	mkdir -p build/logs/phpcs
	${PHPCS}

pcov-enable:
	sudo php-ext-enable pcov

pcov-disable:
	sudo php-ext-disable pcov

help:
	# Usage:
	#   make <target> [OPTION=value]
	#
	# Targets:
	#   clean               		Cleans the coverage and the vendor directory
	#   code-style          		Check codestyle using phpcs
	#   help                		You're looking at it!
	#   pcov-enable         		Enable pcov
	#   pcov-disable        		Disable pcov
