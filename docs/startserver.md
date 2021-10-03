# Start server bash script

[Github direct link](https://github.com/nickyeoman/phpframework/blob/main/bin/startServer.bash)

You can run the start server script from the root of your project directory like so:
```bash
sudo bash vendor/nickyeoman/phpframework/bin/startServer.bash
```

This will check your .env file for the following:

* USEDOCKER
* DOCKERPORT
* DOCKERIMAGE
* DOCKERVER
* DOCKERVOL
* DOCKERNAME
* DOCKERNET
* DOCKERDB
* DBPASSWORD
* DBUSER
* DB
* DBPORT
* DOCKERPHPMYADMIN

docker section is complete, or change USERDOCKER="php".
