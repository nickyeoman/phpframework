# Nick Yeoman's phpframework

## Links

* [View on GitHub](https://github.com/nickyeoman/phpframework)
* [View on Composer's Packagist](https://packagist.org/packages/nickyeoman/phpframework)

## Things you need to know

1. Always run scripts from the project root directory (composer.json, .env also live here).

## Requirements

* ubuntu php-cli (sudo nala install php-curl php-xml php-mysql)
* [Install Composer](https://www.nickyeoman.com/page/install-composer-on-ubuntu) 
## Easy Install

Just change "YOUR_PROJECT_NAME" to whatever you want.

```bash
wget fbot.co/nyphp
bash nyphp YOUR_PROJECT_NAME
rm nyphp
```
Now you should change your [dotenv](https://github.com/nickyeoman/phpframework/blob/main/docs/dotenv.md) file;

Then run ```sudo docker-compose up -d```.

Your development server is configured, use [sass](https://github.com/nickyeoman/phpframework/blob/main/docs/sass.md) to modify the look.

## Framework Documentation

* [dotenv](https://github.com/nickyeoman/phpframework/tree/main/docs)
* [sass](https://github.com/nickyeoman/phpframework/blob/main/docs/sass.md)
* [tracy](https://github.com/nickyeoman/phpframework/blob/main/docs/tracy.md)
* [database](https://github.com/nickyeoman/phpframework/blob/main/docs/database.md)
* [robotloader](https://github.com/nickyeoman/phpframework/blob/main/docs/robotloader.md)
* [twig](https://github.com/nickyeoman/phpframework/blob/main/docs/twig.md)
* [users](https://github.com/nickyeoman/phpframework/blob/main/docs/users.md)

## Framework Philosophies

1. Build websites fast
1. A url should be modern, no GET statements (question marks).
1. You should not have to define controllers, routes should be based on the url and the system should be able to figure them out.
1. Docker is king
1. Apache is not that bad

I went with bash for the automation process for two reasons:

1. I'm more familiar with bash.
1. It seemed more appropriate when working with docker containers.
