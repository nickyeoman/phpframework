<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Pages extends AbstractMigration
{

    public function change(): void
    {
      $pages = $this->table('pages');
        $pages->addColumn('title', 'string', ['limit' => 255])
              ->addColumn('slug', 'string', ['limit' => 255])
              ->addColumn('intro', 'text', ['null' => true])
              ->addColumn('body', 'text', ['null' => true])
              ->addIndex(['slug'], ['unique' => true])
              ->create();
    }
}
