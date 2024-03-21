<?php
namespace Nickyeoman\Framework\Controllers\Pages;

use Nickyeoman\Framework\Classes\BaseController;
USE Nickyeoman\Dbhelper\Dbhelp as DB;
USE Nickyeoman\Framework\Components\page\pageHelper as pageHelp;
use Nickyeoman\Framework\Attributes\Route;
use Nickyeoman\Framework\Classes\paginationHelper;

class AdminListPages extends BaseController {

  private $DB;

  // List pages to edit
  #[Route('/admin/pages/{pagenum}')]
  function admin($pagenum = 1) {

    $s = $this->session;
		$v = $this->viewClass;

    if ( ! $s->loggedin() ) {

      $s->addFlash("You need to login to edit pages.",'notice');
      $s->writeSession();
      redirect('/admin');

    } elseif ( !$s->isAdmin() ) {

      $s->addFlash("You need Admin permissions to edit pages.",'notice');
      $s->writeSession(); 
      redirect('/login');

    }

    $current_page = isset($pagenum) && is_numeric($pagenum) && $pagenum >= 1 ? $pagenum : 1;
    $whereCondition = "";
    $limit = 20;
    $this->DB = new DB();
    $pagination = $this->getPagination($current_page, $limit);
    
    $this->viewClass->data['menuActive'] = 'admin-pages';
    $this->viewClass->data['pagination'] = $pagination->data;
    $this->viewClass->data['pageid'] = 'admin-pages';
    
    // Join the tags
    $sql = <<<EOSQL
    SELECT p.id, p.title, p.slug, p.draft, GROUP_CONCAT(t.title SEPARATOR ', ') as 'tags'
    FROM pages p
    LEFT JOIN tag_pages tp ON p.id = tp.pages_id
    LEFT JOIN tags t ON t.id = tp.tag_id
    GROUP BY p.id
    LIMIT $pagination->sql_limit
EOSQL;

    // Run the query
    $result = $this->DB->query($sql);

    if ( !empty( $result ) ) {

      foreach ($result as $key => $value){
        
        // twig wants a tag array
        if ( ! empty($value['tags']) )
          $value['tags'] = array_map('trim', explode(',',$value['tags']));

        // put the values into the view
        $v->data['pages'][$key] = $value;

      }

    }

    $v->set('pageid', "page-admin");
    $v->set('adminbar', true);
    $s->writeSession();
    //$this->twig('admin', $v->data, 'page');
    $this->view('@cms/pages/admin');

  }
  //end admin

  private function getPagination($current_page, $limit) {
    $sql = "SELECT COUNT(*) as i FROM pages p ";
    $result = $this->DB->query($sql);
    $num_articles = $result[0]['i'];
    return new paginationHelper($limit, $current_page, $num_articles);
  }

} //end class
