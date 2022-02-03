<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PagesDatabaseCreation extends AbstractMigration
{

    public function change(): void {

      $pages = $this->table('pages');
        $pages->addColumn('title', 'string', ['limit' => 255])
              ->addColumn('heading', 'string', ['limit' => 255])
              ->addColumn('description', 'string', ['limit' => 255])
              ->addColumn('keywords', 'string', ['limit' => 255])
              ->addColumn('author', 'string', ['limit' => 255])
              ->addColumn('slug', 'string', ['limit' => 255])
              ->addColumn('tags', 'string', ['limit' => 255])
              ->addColumn('intro', 'text', ['null' => true])
              ->addColumn('body', 'text', ['null' => true])
              ->addColumn('notes', 'text', ['null' => true])
              ->addColumn('created', 'datetime')
              ->addColumn('updated', 'datetime', ['null' => true])
              ->addColumn('draft', 'boolean', ['default' => 1])
              ->addColumn('changefreq', 'string', ['limit' => 7])
              ->addColumn('priority', 'decimal')
              ->addIndex(['slug'], ['unique' => true])
              ->create();
    }
}
