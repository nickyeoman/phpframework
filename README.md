# Nick Yeoman's phpframework

This is Nix framework, it is my favourite framework because it is my framework.
If this was your framework you would probably like it more.

# Links

* [View on GitHub](https://github.com/nickyeoman/phpframework)
* [View on Composer's Packagist](https://packagist.org/packages/nickyeoman/phpframework)

# Install using Composer

You need a basic understanding of bash, replace the <YOUR_PROJECT_PATH/YOUR_PROJECT_NAME>.
Always run your scripts from the project root directory.

```bash
mkdir <YOUR_PROJECT_PATH/YOUR_PROJECT_NAME>
cd <YOUR_PROJECT_PATH/YOUR_PROJECT_NAME>
composer require nickyeoman/phpframework
bash vendor/nickyeoman/phpframework/bin/newProject.bash
```

After you edit the env file.
Then you will want to add a controller:

```bash
bash vendor/nickyeoman/phpframework/bin/newController.bash index
```

## Start server

sudo bash vendor/nickyeoman/phpframework/bin/startServer.bash

Make sure your dotenv is complete

# Framework Components

## Composer

The whole idea is to play nice with composer, to save time.

## ENV Variables

ENV Variables: https://github.com/vlucas/phpdotenv

## Tracy Debugger

I "must have" debugging tool: https://tracy.nette.org/

To enable Tracy Debug bar, change .env variable "DEBUG" to "display".
Any other value will disable tracy.

## Nette RobotLoader

Auto loads Controllers
https://doc.nette.org/en/3.1/robotloader

## Uses Twig templates

The view function calls on twig templates for displaying the views.

## Database redbean

Readbean does so much work for you.
