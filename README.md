# Nick Yeoman's phpframework

This is Nix framework, it is my favourite framework because it is my framework.
If this was your framework you would probably like it more.

# Links

* [View on GitHub](https://github.com/nickyeoman/phpframework)
* [View on Composer's Packagist](https://packagist.org/packages/nickyeoman/phpframework)

# Install using Composer

You need a basic understanding of bash, replace the <YOUR_PROJECT_PATH/YOUR_PROJECT_NAME>.
Always run your scripts from the project root directory.
[Install Composer](https://www.nickyeoman.com/blog/php/install-composer-on-ubuntu/) if you have not done so already.

```bash
mkdir <YOUR_PROJECT_PATH/YOUR_PROJECT_NAME>
cd <YOUR_PROJECT_PATH/YOUR_PROJECT_NAME>
composer require nickyeoman/phpframework
bash vendor/nickyeoman/phpframework/bin/newProject.bash
```

Then you will want to add a controller:

```bash
bash vendor/nickyeoman/phpframework/bin/newController.bash index
```

## Start server

Make sure your dotenv Docker section is complete, or change USERDOCKER="php".

sudo bash vendor/nickyeoman/phpframework/bin/startServer.bash


# Framework Components

## Composer

The whole idea is to play nice with composer, to save time.

## ENV Variables

ENV Variables: https://github.com/vlucas/phpdotenv

## Tracy Debugger

A "must have" debugging tool: https://tracy.nette.org/

To enable Tracy Debug bar, change .env variable "DEBUG" to "display".
Any other value will disable tracy.

## Nette RobotLoader

Auto loads Controllers
https://doc.nette.org/en/3.1/robotloader

## Uses Twig templates

The view function calls on twig templates for displaying the views.

# Framework Philosophies

1. Build websites fast
1. A url should be modern, no GET statements (question marks).
1. You should not have to define controllers, routes should be based on the url and the system should be able to figure them out. (CodeIgniter style)
1. Docker is king
1. Framework should play nice with hugo (Apache settings)
1. Apache is okay
1. I went with bash for the automation process for two reasons, one I'm more familiar with it and two it seemed more appropriate when working with containers.
