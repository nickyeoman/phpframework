<?php
namespace Nickyeoman\Framework\Controllers\Pages;

use Nickyeoman\Framework\Classes\BaseController;
USE Nickyeoman\Dbhelper\Dbhelp as DB;
USE Nickyeoman\Framework\Components\page\pageHelper as pageHelp;

class pagesAdmin extends BaseController {

  // List pages to edit
  function admin() {

    $s = $this->session;
		$v = $this->viewClass;

    if ( ! $s->loggedin() ) {

      $s->addflash("You need to login to edit pages.",'notice');
      $s->writeSession();
      redirect('admin', 'index');

    } elseif ( !$s->isAdmin() ) {

      $s->addflash("You need Admin permissions to edit pages.",'notice');
      $s->writeSession(); 
      redirect('user', 'login');

    }

    //Grab pages
    $DB = new DB();
    
    // Join the tags
    $sql = <<<EOSQL
    SELECT p.id, p.title, p.slug, p.draft, GROUP_CONCAT(t.title SEPARATOR ', ') as 'tags'
    FROM pages p
    LEFT JOIN tag_pages tp ON p.id = tp.pages_id
    LEFT JOIN tags t ON t.id = tp.tag_id
    GROUP BY p.id
EOSQL;

    // Run the query
    $result = $DB->query($sql);
    

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
    $this->view('@page/admin');

  }
  //end admin

  /** 
   * Edit a page
   **/
  function edit($params = null) {

    $s = $this->session;
		$v = $this->viewClass;
		$r = $this->request;
    $DB = new DB();
    
    if ( ! $s->loggedin('You need to login to edit pages.') )
      redirect('admin', 'index');

    if ( !$s->isAdmin() ) {
      $s->addflash('You need to be an admin to view messages..','error');
      redirect('user', 'login');
    }

    if ( $r->submitted ) {

      $page = new pageHelp($r->post);
      
      $DB->update('pages', $page->page , 'id' );
      $DB->modifyTags('pages', $page->page['id'], $page->tags );
      $DB->close();

      $s->addflash('Saved Page.','notice');
      $s->writeSession();

      redirect('page', 'admin');

    }

    $pid = $params['id'];

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
    
    $this->view('@page/edit');

  }
  // end function edit

  public function new(){

    $s = $this->session;
		$v = $this->viewClass;
		$r = $this->request;

    if ( ! $s->loggedin() ) {

      $s->addflash('You need to login to edit pages.','notice');
      redirect('login', 'index');

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

      $s->addflash('Saved Page.','notice');
      $this->redirect('page', 'admin');

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
    $this->view('@page/edit');

  }
  //end new

  public function tagadmin($params = null) {

    $s = $this->session;
		$v = $this->viewClass;
		$r = $this->request;

    if ( ! $s->loggedin() ) {

      $s->addflash("You need to login to edit tags.",'notice');
      redirect('user', 'login');

    } elseif ( !$s->inGroup('admin') ) {

      $s->addflash("You need Admin permissions to edit tags.",'notice');
      redirect('user', 'login');

    }

    $tag = $params[0];
    if ( empty( $tag ) )
      die("no tag supplied, TODO: 404");

    $DB = new DB();      
    $result = $DB->findall('pages', '*', "`tags` LIKE '%$tag%'");
    foreach ($result as $key => $value){
      $value['tags'] = explode(',',$value['tags']);
      $this->data['pages'][$key] = $value;
    }
    $DB->close();

    $v->set('adminbar', true);
    $v->set('pageid', "page-admin");
    $view('@page/admin');
  }

  public function admincomment($params = null) {

    $s = $this->session;
		$v = $this->viewClass;
		$r = $this->request;
    $DB = new DB();

    if ( ! $s->loggedin('You need to login to admin comments.') )
      $this->redirect('user', 'login');

    if ( !$s->inGroup('admin', 'You need admin permissions;') )
      $this->redirect('user', 'login');

    // Remove a comment
    if ( !empty($params) ) {
      if ( $params[0] == 'delete' && $params[1] >= 0 ) {
        $DB->delete('comments', "`id` = $params[1]");
        $this->adderror("Removed comment $params[1]", 'notice');
      }
    }


    //Grab pages
    $SQL = <<<EOSQL
SELECT
  c.id, c.body, u.username, c.date
FROM
  `comments` as c
LEFT JOIN
  `users` as u
ON
  c.userid = u.id
EOSQL;

    $v->data['comments'] = $DB->query($SQL);

    $v->data['pageid'] = "comments-admin";
    $v->set('adminbar', true);
    $this->view('@page/adminComments');
  }

} //end class
