<?php
namespace Nickyeoman\Framework\Controllers\Pages;

use Nickyeoman\Framework\Classes\BaseController;
USE Nickyeoman\Dbhelper\Dbhelp as DB;
USE Nickyeoman\Framework\Helpers\pageHelper as pageHelp;
use Nickyeoman\Framework\Attributes\Route;
use Nickyeoman\Framework\Classes\paginationHelper; // TODO: Needs pagination

class AdminEditPage extends BaseController {

  /** 
   * Edit a page
   **/
  #[Route('/admin/pages/edit/{pageid}', methods: ['GET', 'POST'])]
  function edit($pageid) {

    $s = $this->session;
    $v = $this->viewClass;
    $r = $this->request;
    $DB = new DB();
    
    if ( ! $s->loggedin('You need to login to edit pages.') )
      redirect('/login');

    if ( !$s->isAdmin() ) {
      $s->addFlash('You need to be an admin to view messages..','error');
      redirect('/admin');
    }

    if ( $r->submitted ) {

      $page = new pageHelp($r->post);
      
      $DB->update('pages', $page->page , 'id' );
      $DB->modifyTags('pages', $page->page['id'], $page->tags );
      $DB->close();

      $s->addflash('Saved Page.','notice');
      $s->writeSession();

      redirect('/admin/pages/edit/'. $page->page['id']);

    }

    $pid = $pageid;

    if ( empty($pid) ) {
      //TODO: error, page not given
      //TODO: clean it up, trim, lower
    }
    
    // Join the tags
    $sql = <<<EOSQL
        SELECT p.*, GROUP_CONCAT(t.title SEPARATOR ', ') as 'tags'
        FROM pages p
        LEFT JOIN tag_pages tp ON p.id = tp.pages_id
        LEFT JOIN tags t ON t.id = tp.tag_id
        WHERE p.id = $pid
        GROUP BY p.id
    EOSQL;

    $results = $DB->query($sql);
    $DB->close();
    $v->data['info'] = $results[0];
    
    $v->data['pageid'] = "page-edit";
    
    $this->view('@cms/pages/edit');

  }
  // end function edit

  #[Route('/admin/pages/new', methods: ['GET','POST'])]
  public function new(){

    $s = $this->session;
		$v = $this->viewClass;
		$r = $this->request;

    if ( ! $s->loggedin() ) {

      $s->addFlash('You need to login to edit pages.','notice');
      redirect('/login');

    }

    if ( $r->submitted ) {
      
      $page = new pageHelp($r->post);

      if( empty($page->error) ) {
        $DB = new DB();
        $insertid = $DB->create('pages', $page->page);
        $DB->modifyTags('pages', $insertid, $page->tags );
        $DB->close();

      } else {
        dump($page->error);die("there are page errors");
      }

      $s->addFlash('Saved Page.','notice');
      redirect('/admin/pages');

    }
    //end submitted

    $v->data['info'] = array(
    'title' => 'New Title',
    'slug'  => 'slug',
    'intro' => 'Placeholder Text',
    'body'  => 'Placeholder Text'
    );
    $v->data['mode'] = 'new';
    $v->data['pageid'] = "page-edit";
    $s->writeSession();
    $this->view('@cms/pages/edit');

  }
  //end new

} //end class
