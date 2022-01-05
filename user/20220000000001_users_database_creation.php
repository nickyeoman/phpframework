<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UsersDatabaseCreation extends AbstractMigration
{

    public function change(): void
    {
      $users = $this->table('users');
        $users->addColumn('username', 'string', ['limit' => 30])
              ->addColumn('password', 'string', ['limit' => 70])
              ->addColumn('email', 'string', ['limit' => 255])
              ->addColumn('first_name', 'string', ['null' => true, 'limit' => 70])
              ->addColumn('last_name', 'string', ['null' => true, 'limit' => 70])
              ->addColumn('validate', 'string', ['null' => true, 'limit' => 32])
              ->addColumn('reset', 'string', ['null' => true, 'limit' => 32])
              ->addColumn('created', 'datetime')
              ->addColumn('updated', 'datetime', ['null' => true])
              ->addColumn('blocked', 'boolean', ['default' => 0])
              ->addColumn('admin', 'boolean', ['default' => 0])
              ->create();
    }
}
