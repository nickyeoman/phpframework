<?php
namespace Nickyeoman\Framework\Components\page;

USE Nickyeoman\Framework\SessionManager;
USE Nickyeoman\Framework\ViewData;
USE Nickyeoman\Framework\RequestManager;
USE \Nickyeoman\Dbhelper\Dbhelp as DB;
USE Nickyeoman\Framework\Components\page\pageHelper as pageHelp;


class pageController extends \Nickyeoman\Framework\BaseController {

  // There is no index page, this will catch
  function override( $params = array() ) {

    $s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

    //TODO: 404 if empty
    $slug = $params[0];

    $DB = new DB();
    $pagedata = $DB->findone('pages', 'slug', $slug);

    if ( empty($pagedata)) {
      // TODO: test this
      header('HTTP/1.1 404 Not Found');
      $this->twig('404', $v->data, 'error');
      die();
    }

    foreach( $pagedata as $key => $value ){
      if (!empty($value))
        $pagedata[$key] = html_entity_decode($value);
      else
        $pagedata[$key] = null;
    }

    $taglinks = explode(',',$pagedata['tags']);
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
SELECT
	c.body, u.username, c.date
FROM
	`comments` as c
LEFT JOIN
	`users` as u
ON
	c.userid = u.id
WHERE
	c.pageid = $pageid
EOSQL;

    $v->data['comments'] = $DB->query($SQL);
    $DB->close();
    $v->data['page'] = $pagedata;

    $s->writeSession();
    $this->twig('page', $v->data, 'page');

  }
  // end override

  // List pages to edit
  function admin() {

    $s = new SessionManager();
		$v = new ViewData($s);

    if ( ! $s->loggedin() ) {

      $s->addflash("You need to login to edit pages.",'notice');
      $s->writeSession();
      $this->redirect('admin', 'index');

    } elseif ( !$s->isAdmin() ) {

      $s->addflash("You need Admin permissions to edit pages.",'notice');
      $s->writeSession(); 
      $this->redirect('user', 'login');

    }

    //Grab pages
    $DB = new DB();
    $result = $DB->findall('pages','id,title,slug,draft');

    if ( !empty( $result ) ) {
      foreach ($result as $key => $value){
        $value['tags'] = explode(',',$value['tags']);
        $v->data['pages'][$key] = $value;
      }
    }

    $v->set('pageid', "page-admin");
    $v->set('adminbar', true);
    $s->writeSession();
    $this->twig('admin', $v->data, 'page');

  }
  //end admin

  /** 
   * Edit a page
   **/
  function edit($params = null) {

    $s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);
    $DB = new DB();
    
    if ( ! $s->loggedin('You need to login to edit pages.') )
      $this->redirect('admin', 'index');

    if ( !$s->isAdmin() ) {
      $s->addflash('You need to be an admin to view messages..','error');
      $this->redirect('user', 'login');
    }

    if ( $r->submitted ) {

      $page = new pageHelp($r->post);
      
      
      $id = $DB->update('pages', $page->page , 'id' );

      $s->addflash('Saved Page.','notice');
      $s->writeSession();
      $this->redirect('page', 'admin');

    }

    $pid = $params[0];

    if ( empty($pid) ) {
      //TODO: error, page not given
      //TODO: clean it up, trim, lower
    }

    $v->data['info'] = $DB->findone('pages', 'id', $pid);
    $v->data['pageid'] = "page-edit";
    $this->twig('edit', $v->data, 'page');

  }
  // end function edit

  public function new(){

    $s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

    if ( ! $s->loggedin() ) {

      $s->addflash('You need to login to edit pages.','notice');
      $this->redirect('login', 'index');

    }

    if ( $r->submitted ) {
      
      $page = new pageHelp($r->post);

      if( empty($page->error) ) {
        $DB = new DB();
        $DB->create('pages', $page->page);
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
    $this->twig('edit', $v->data, 'page');

  }
  //end new

  public function tagadmin($params = null) {

    $s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

    if ( ! $s->loggedin() ) {

      $s->addflash("You need to login to edit tags.",'notice');
      $this->redirect('user', 'login');

    } elseif ( !$s->inGroup('admin') ) {

      $s->addflash("You need Admin permissions to edit tags.",'notice');
      $this->redirect('user', 'login');

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
    $this->twig('admin', $this->data, 'page');
  }

  public function createcomment() {

    $s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);

    // Check if logged in
    if ( ! $s->loggedin('You need to login to leave a comment.') )
      $this->redirect('user', 'login');

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

    $this->redirect('page', $this->post['slug']);

  }

  public function admincomment($params = null) {

    $s = new SessionManager();
		$v = new ViewData($s);
		$r = new RequestManager($s, $v);
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
    $this->twig('adminComments', $v->data, 'page');
  }

} //end class
