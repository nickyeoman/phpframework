<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class LogsDatabaseCreation extends AbstractMigration
{

    public function change(): void
    {
      $logs = $this->table('logs');
        $logs->addColumn('level', 'string', ['limit' => 30])
              ->addColumn('title', 'string', ['limit' => 255])
              ->addColumn('location', 'string', ['limit' => 255])
              ->addColumn('ip', 'string', ['limit' => 255])
              ->addColumn('url', 'string', ['limit' => 255])
              ->addColumn('content', 'text', ['null' => true])
              ->addColumn('session', 'text', ['null' => true])
              ->addColumn('post', 'text', ['null' => true])
              ->addColumn('time', 'datetime')
              ->create();
    }
}
