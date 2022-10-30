# dotenv

There is a sample dotenv (.env) file in the root directory of this project (env.sample).

You can always search the code to find where these variables are used:
```bash
grep -iIrn "DOTENVNAME" *
```

## Global Variables
### DEBUG

*DEFAULT:* DEBUG="production" (not active)

Default means all debug data is not displayed. 

*display:* DEBUG="display" activates the [Tracy debugger](https://tracy.nette.org/en/).

### CONTROLLERPATH

DEFAULT: CONTROLLERPATH="controllers"

This is the default directory to look for app controllers.

The path is relative from the root of the project directory (The root directory is where the dotenv file is located).

Do not add pre or post slashes.

### VIEWPATH

DEFAULT: VIEWPATH="views"

Same as CONTROLLERPATH but for views.
define the path from the root directory of the project.
do not add pre or post slashes.

### TWIGCACHE

DEFAULT: TWIGCACHE="tmp"
The path twig uses to cache.
From the root project directory.
do not add pre or post slashes.

### LOADERTMPDIR

This is the tmp directory.
LOADERTMPDIR="tmp"

### SALT

I generate my SALT with pwgen -cnsB1v 32

### BASEURL
Requires last slash.
If left blank? (how do we allow for anything?)
---

### LOGGING

DEFAULT=off
tells the cms where to log.
mysql is currently the only option
function log in BaseController.

### USECMS

The framework has a build in CMS components. If this is false the router will not load them.

## Docker

USEDOCKER="docker"
DOCKERVOL="local"
DOCKERNAME="YOURNAME"
DOCKERIMAGE="ORG/PROJECT"
DOCKERVER="latest"
DOCKERPORT="8000"
DOCKERNET="DOCKER_NETWORK_NAME_FOR_PROXY"
DOCKERDB="none"
DOCKERPHPMYADMIN=8002

## Database
DBHOST="127.0.0.1"
DBPORT="3301"
DBUSER=""
DBPASSWORD=""
DB=""

If you are not using a database, make sure that the DBUSER is null or empty.

## SMTP
MAIL_MAILER="smtp"
MAIL_HOST=""
MAIL_PORT="587"
MAIL_USERNAME=""
MAIL_PASSWORD=""
MAIL_ENCRYPTION="tls"
MAIL_FROM_ADDRESS=""
MAIL_FROM_NAME=""

## IMAP
IMAP_SERVER=imap.gmail.com:993/imap/ssl}INBOX
IMAP_USER=example@gmail.com
IMAP_PASSWORD=examplePasswordToAccount

# Debugging dotenv

Framework environment variables are pulled in using [phpdotenv]( https://github.com/vlucas/phpdotenv)
