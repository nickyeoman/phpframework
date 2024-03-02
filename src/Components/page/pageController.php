<?php
namespace Nickyeoman\Framework\Components\page;

use Nickyeoman\Framework\Classes\BaseController;
USE Nickyeoman\Dbhelper\Dbhelp as DB;
USE Nickyeoman\Framework\Components\page\pageHelper as pageHelp;

class pageController extends BaseController {

  function page( $params = array() ) {

    $s = $this->session;
		$v = $this->viewClass;

    //TODO: 404 if empty
    $slug = $params['slug'];

    $DB = new DB();
    $sql = <<<EOSQL
        SELECT p.*, GROUP_CONCAT(t.title SEPARATOR ', ') as 'tags'
        FROM pages p
        LEFT JOIN tag_pages tp ON p.id = tp.pages_id
        LEFT JOIN tags t ON t.id = tp.tag_id
        WHERE p.slug = '$slug'
        GROUP BY p.id
EOSQL;
    
    $result = $DB->query($sql);
    if ( $result !== null )
      $pagedata = $result[0];
    
    //$pagedata = $DB->findone('pages', 'slug', $slug);

    if ( empty($pagedata)) {
      // TODO: test this
      header('HTTP/1.1 404 Not Found');
      $this->view('@error/404');
      die();
    }

    foreach( $pagedata as $key => $value ){
      if (!empty($value))
        $pagedata[$key] = html_entity_decode($value);
      else
        $pagedata[$key] = null;
    }

    
    $taglinks = array_map('trim', explode(',',$pagedata['tags']));
    $pagedata['taglinks'] = ''; //string

    foreach ( $taglinks as $value) {
      // TODO: this only works with Nick's website
      $pagedata['taglinks'] .= ' <a class="taglinks" href="/blog/tag/' . $value . '/1">' . $value . '</a>';
    }

    if ( $s->loggedin() )
      $v->set('userid', $s->getKey('userid'));

    // Comments
    $pageid = $pagedata['id']; // Used in multiple places
    $s->setKey('pageid', $pageid); //remember in session
    $SQL = <<<EOSQL
SELECT c.body, u.username, c.date
FROM comments c
LEFT JOIN users u ON c.userid = u.id
WHERE c.pageid = $pageid
EOSQL;

    $v->data['comments'] = $DB->query($SQL);
    $DB->close();
    $v->data['page'] = $pagedata;

    $s->writeSession();
    $this->view('@page/page');

  }
  // end page

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

  public function createcomment() {

    $s = $this->session;
		$v = $this->viewClass;
		$r = $this->request;

    // Check if logged in
    if ( ! $s->loggedin('You need to login to leave a comment.') )
      redirect('user', 'login');

    if ( empty( $s->getKey('pageid') ))
      $s->addflash("Error: missing pageid.",'error');

    if ( empty( $r->get('comment') )) {

      $s->addflash("You need content in the comment",'error');

    } else {

      // The comment
      // load validation
      $this->valid = new \Nickyeoman\Validation\Validate();
      // Check the comment against spammy words using the validate class
      $badwords = '';
      $badwords = $this->valid->checkSpam($this->post['comment'], $_ENV['SPAMWORDS']);

      if ( !empty($badwords) )
        $s->addflash("You can't use these words: $badwords",'error');

    }

    if ( $s->flashcount('error') < 1 ) {

      $comment = array(
        'pageid'  => $s->getKey('pageid')
        ,'userid' => $s->getKey('id')
        ,'body'   => $r->get('comment')
      );

      $DB = new DB();
      $id = $this->db->create('comments', $comment);
      $DB->close();
      $s->addflash("Saved Comment.",'notice');

    }

    redirect('page', $this->post['slug']);

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
