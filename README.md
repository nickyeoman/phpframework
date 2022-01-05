# Nick Yeoman's phpframework

## Links

* [View on GitHub](https://github.com/nickyeoman/phpframework)
* [View on Composer's Packagist](https://packagist.org/packages/nickyeoman/phpframework)

## Things you need to know

1. Always run bin scripts from the project root directory.

## Two line Install

Just change "YOUR_PROJECT_NAME" to whatever you want.

```bash
wget fbot.co/nyphp
bash nyphp YOUR_PROJECT_NAME
rm nyphp
```
Now you should change your [dotenv](https://github.com/nickyeoman/phpframework/tree/main/docs) file;
then you should run [startServer](https://github.com/nickyeoman/phpframework/tree/main/docs).

Your development server is configured, use [sass](https://github.com/nickyeoman/phpframework/tree/main/docs) to modify the look.

## Framework Documentation

* [dotenv](https://github.com/nickyeoman/phpframework/tree/main/docs)
* [startServer](https://github.com/nickyeoman/phpframework/tree/main/docs)
* [sass](https://github.com/nickyeoman/phpframework/tree/main/docs)
* [tracy](/home/nick/github/phpframework/README.md)
* [database](https://github.com/nickyeoman/phpframework/tree/main/docs)
* [robotloader](https://github.com/nickyeoman/phpframework/tree/main/docs)
* [twig](https://github.com/nickyeoman/phpframework/tree/main/docs)
* [users](https://github.com/nickyeoman/phpframework/tree/main/docs)

## Framework Philosophies

1. Build websites fast
1. A url should be modern, no GET statements (question marks).
1. You should not have to define controllers, routes should be based on the url and the system should be able to figure them out. (CodeIgniter style)
1. Docker is king
1. Framework should play nice with hugo (Apache settings)
1. Apache is okay

I went with bash for the automation process for two reasons:
1. I'm more familiar with bash.
1. It seemed more appropriate when working with docker containers.
