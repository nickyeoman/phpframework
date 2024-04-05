# .env File Documentation

Below is a guide to understanding and configuring the `.env` file for this project. This file contains various environment variables used throughout the application.

## Global Variables

### DEBUG

- **production(Default):** DEBUG="production" (inactive)
- **display:** DEBUG="display" (activates the [Tracy debugger](https://tracy.nette.org/en/))

Controls the debug mode of the application. When set to "display", debug data will be shown.

Found in:
* inc/tracy.php:6
* inc/utility.php:37
* src/Classes/ViewData.php:107
* src/Classes/ViewData.php:121

### VIEWPATH

- **Default:** VIEWPATH="App/Views"

Specifies the default directory for application views. The path is relative to the project's root directory.

Found in:
* inc/twig.php:8

### SALT

Generates a unique salt used for encryption purposes. You can generate a salt using tools like `pwgen -cnsB1v 32`.

Found in:
* src/Components/user/userHelper.php

### BASEURL

Specifies the base URL of the application. Ensure it ends with a slash.

Found in:
* src/Controllers/sitemapController.php:41
* src/Controllers/sitemapController.php:43
* src/Components/user/userHelper.php:208
* src/Components/user/userHelper.php:251


### LOGGING

- **Default:** LOGGING="off"

Controls where the application logs its data. Currently, only MySQL is supported.

Found in:
* src/Classes/Logger.php

## Docker Configuration

These variables are used when running the application within Docker containers.

### DOCKERIMAGE

- **Default:** DOCKERIMAGE="4lights/phpcontainer"

Specifies the Docker image to be used.

### DOCKERVER

- **Default:** DOCKERVER="latest"

Specifies the version of the Docker image.

### DOCKERPORT

- **Default:** DOCKERPORT="8000"

Specifies the port used by Docker.

### DOCKERNET

- **Default:** DOCKERNET="DOCKER_NETWORK_NAME_FOR_PROXY"

Specifies the Docker network name for proxy.

### DOCKERPHPMYADMINPORT

- **Default:** DOCKERPHPMYADMINPORT="8001"

Specifies the port used by PhpMyAdmin when running with Docker.

## Database Configuration

These variables are used for configuring the database connection.

### MYSQL_ROOT_PASSWORD

Specifies the root password for MySQL.

### DBHOST

- **Default:** DBHOST="phpframework-db"

Specifies the host for the database.

### DBPORT

- **Default:** DBPORT="3306"

Specifies the port for the database connection.

### DBUSER

Specifies the username for the database connection.

### DBPASSWORD

Specifies the password for the database connection.

### DB

Specifies the name of the database.

## SMTP Configuration

These variables are used for configuring SMTP settings.

### MAIL_MAILER

- **Default:** MAIL_MAILER="smtp"

Specifies the mailer to be used.

### MAIL_HOST

Specifies the SMTP host.

### MAIL_PORT

Specifies the port for SMTP connection.

### MAIL_USERNAME

Specifies the username for SMTP authentication.

### MAIL_PASSWORD

Specifies the password for SMTP authentication.

### MAIL_ENCRYPTION

Specifies the encryption method for SMTP.

### MAIL_FROM_ADDRESS

Specifies the sender's email address.

### MAIL_FROM_NAME

Specifies the sender's name.

## IMAP Configuration

These variables are used for configuring IMAP settings.

### IMAP_SERVER

Specifies the IMAP server details.

### IMAP_USER

Specifies the IMAP username.

### IMAP_PASSWORD

Specifies the IMAP password.

# Debugging .env

Framework environment variables are loaded using [phpdotenv](https://github.com/vlucas/phpdotenv).
