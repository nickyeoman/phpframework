# dotenv

There is a sample dotenv (.env) file in the root directory of this project (env.sample).

You can always search the code to find where these variables are used:
```bash
grep -iIrn "DOTENVNAME" *
```

Details per variable:

## DEBUG
DEFAULT: DEBUG="production" (not active)
DEBUG="display" activates the [Tracy debugger](https://tracy.nette.org/en/).
Set to "display" to show.
Default is removed and hidden,  you may also set any string other than "display" to hide.

## CONTROLLERPATH
DEFAULT: CONTROLLERPATH="controllers"
The default directory to look for app controllers.
relative from the root of the project directory (where the dotenv file is located).
do not add pre or post slashes.

## VIEWPATH
DEFAULT: VIEWPATH="views"
Same as CONTROLLERPATH but for views.
define the path from the root directory of the project.
do not add pre or post slashes.

## TWIGCACHE
DEFAULT: TWIGCACHE="tmp"
The path twig uses to cache.
From the root project directory.
do not add pre or post slashes.

---

LOADERTMPDIR="tmp"
SALT="MakeASalt32charactersWouldBeNice"
BASEURL="http://localhost:8000/"

# Docker
USEDOCKER="docker"
DOCKERVOL="local"
DOCKERNAME="YOURNAME"
DOCKERIMAGE="ORG/PROJECT"
DOCKERVER="latest"
DOCKERPORT="8000"
DOCKERNET="DOCKER_NETWORK_NAME_FOR_PROXY"
DOCKERDB="none"

# Database
DBHOST="127.0.0.1"
DBPORT="3301"
DBUSER=""
DBPASSWORD=""
DB=""

# Email
MAIL_MAILER="smtp"
MAIL_HOST=""
MAIL_PORT="587"
MAIL_USERNAME=""
MAIL_PASSWORD=""
MAIL_ENCRYPTION="tls"
MAIL_FROM_ADDRESS=""
MAIL_FROM_NAME=""
