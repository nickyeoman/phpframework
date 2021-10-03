# Nick Yeoman's phpframework

## Links

* [View on GitHub](https://github.com/nickyeoman/phpframework)
* [View on Composer's Packagist](https://packagist.org/packages/nickyeoman/phpframework)

## Things you need to know

1. Always run bin scripts from the project root directory.

## Two line Install

Just change "YOUR_PROJECT_NAME" to whatever you want.

```bash
wget https://raw.githubusercontent.com/nickyeoman/phpframework/main/bin/newProject.bash
bash newProject.bash YOUR_PROJECT_NAME
```

## Framework Documentation

* [startServer](https://github.com/nickyeoman/phpframework/tree/main/docs)
* [dotenv](https://github.com/nickyeoman/phpframework/tree/main/docs)
* [tracy](/home/nick/github/phpframework/README.md)
* [database](https://github.com/nickyeoman/phpframework/tree/main/docs)
* [sass](https://github.com/nickyeoman/phpframework/tree/main/docs)
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
1. I went with bash for the automation process for two reasons, one I'm more familiar with it and two it seemed more appropriate when working with containers.
