# How to connect to a database

The framework uses RedBeanPHP to connect to a database.
Optionally you can use phinx to control your database migrations.

## Database Migrations with Phinx

Framework uses [Phenix migraitons](https://book.cakephp.org/phinx/0/en/migrations.html)

### Preparation

Ensure that your dotenv file is correct.
Make sure you have a database running (the bash script will create a mariadb docker container for you based on dotenv).
For production it's intended you use your docker-composer file.

```bash
bash vendor/nickyeoman/phpframework/bin/startServer.bash
```

### Using Phinx

First you need to initialize phinx:

```bash
php vendor/bin/phinx init
```

in order to create a phinx.php config file. [Phinx Installation](https://book.cakephp.org/phinx/0/en/install.html)

Next supply phinx.php with your database information.
Note this is left separate as when working in development mode you are connecting from host to container,
where in the application (dotenv) you are connecting container to container.

**TODO: get phix to read from .env file.**

### Create a new migration

php vendor/bin/phinx create FirstMigrationCamelCase

[Writing Phinx Migrations Documentation](https://book.cakephp.org/phinx/0/en/migrations.html)

### Run migration

php vendor/bin/phinx migrate

## Database Queries with RedBeanPHP

Further documentation:

* [RedBeanPHP CRUD documentation](https://redbeanphp.com/index.php?p=/crud)
* [RedBeanPHP Querying documentation](https://redbeanphp.com/index.php?p=/querying)

You must always include the line: use \RedBeanPHP\R as R;
at the top of your controller to access the R::functions

### Create Row

Below is an example of how to use RedBeanPHP to create a row:

```php
//We will place row in "users" table
$user = R::dispense("users");

//Set fields for the row
$user->email    = "sample@email.com";
$user->password = "REALLY_SECURE_PASSWORD";

//Store to database, return a transaction id
$id = R::store( $user );

//error checking
if ( ! empty( $id ) ) {
  return true;
} else {
  //framework's built in error control
  $this->errors['database'] = "User not created in database";
  return false;
}
```

### Get rows

```php
$data['users'] = R::getAll( 'select * from users' );
```

Then in twig:

```php
{% for user in users %}
  id: {{ user.id }} <br />
  name: {{ user.name }} <br />
  email: {{ user.email }}
{% endfor %}
```
