AddressBook
========================

Symfony app where we can add, edit, delete and have an overview of all contacts in address book.

## Techniques used for this app:

* Symfony 3.4
* Doctrine with SQLite
* Twig
* PHP 7.0

## How to install on local computer

`# Install dependecies `

`composer install`

`# Create the doctrine schema `

`php bin/console doctrine:schema:create`

`# Run the application `

`php bin\console server run `

Then it will open http://127.0.0.1:8000 and the application will be running.

## To Run Tests

`vendor/bin/simple-phpunit`