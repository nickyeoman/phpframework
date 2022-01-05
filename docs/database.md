# How to connect to a database

You can use phinx to control your database migrations.

## Database Migrations with Phinx

Framework uses [Phenix migraitons](https://book.cakephp.org/phinx/0/en/migrations.html)

create a new migration.
```
vendor/bin/phinx create MyNewMigration
```

run migration
```
vendor/bin/phinx migrate
```

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

### Create a new migration

php vendor/bin/phinx create FirstMigrationCamelCase

[Writing Phinx Migrations Documentation](https://book.cakephp.org/phinx/0/en/migrations.html)

### Run migration

php vendor/bin/phinx migrate

## Database Queries

Further documentation:

* [mysqli-database-class](https://packagist.org/packages/thingengineer/mysqli-database-class)

### Create Row

```php
$data = Array ("login" => "admin",
               "firstName" => "John",
               "lastName" => 'Doe'
);
$id = $db->insert ('users', $data);
if($id)
    echo 'user was created. Id=' . $id;
```

### Get rows

```php
$users = $db->get('users'); //contains an Array of all users
$users = $db->get('users', 10); //contains an Array 10 users
```

Then in twig:

```php
{% for user in users %}
  id: {{ user.id }} <br />
  name: {{ user.name }} <br />
  email: {{ user.email }}
{% endfor %}
```
