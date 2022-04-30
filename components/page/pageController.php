<?php
class pageController extends Nickyeoman\Framework\BaseController {

  // There is no index page, this will catch
  function override( $params = array() ) {

    //TODO: 404 if empty
    $slug = $params[0];
    $pagedata = $this->db->findone('pages', 'slug', $slug);

    if ( empty($pagedata)) {
      header('HTTP/1.1 404 Not Found');
      $this->twig('404', $this->data);
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

    if ( $this->session->loggedin() )
      $this->data['userid'] = $this->session->getKey('userid');

    // Comments
    $pageid = $pagedata['id']; // Used in multiple places
    $this->session->setKey('pageid', $pageid); //remember in session
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

    $this->data['comments'] = $this->db->query($SQL);
    $this->data['page'] = $pagedata;

    $this->session->writeSession();
    $this->twig('page/page', $this->data);

  }
  // end override

  // List pages to edit
  function admin() {

    if ( ! $this->session->loggedin() ) {

      $this->session->addflash("You need to login to edit pages.",'notice');
      $this->session->writeSession();
      $this->redirect('user', 'login');

    } elseif ( !$this->session->inGroup('admin') ) {

      $this->session->addflash("You need Admin permissions to edit pages.",'notice');
      $this->session->writeSession();
      $this->redirect('user', 'login');

    }

    //Grab pages
    $result = $this->db->findall('pages','id,title,slug,tags,draft');

    if ( !empty( $result ) ) {
      foreach ($result as $key => $value){
        $value['tags'] = explode(',',$value['tags']);
        $this->data['pages'][$key] = $value;
      }
    }

    $this->data['pageid'] = "page-admin";
    $this->session->writeSession();
    $this->twig('page/admin', $this->data);

  }
  //end admin

  // Edit a page
  function edit($params = null) {

    if ( ! $this->session->loggedin('You need to login to edit pages.') )
      $this->redirect('user', 'login');

    if ( !$this->session->inGroup('admin', 'You need Admin permissions to edit pages.') )
      $this->redirect('user', 'login');

    if ( $this->post['submitted'] ) {
      $page = new Nickyeoman\helpers\pageHelper($this->post);

      $id = $this->db->update('pages', $page->page , 'id' );

      $this->session->addflash('Saved Page.','notice');
      $this->session->writeSession();
      $this->redirect('page', 'admin');

    }

    $pid = $params[0];

    if ( empty($pid) ) {
      //TODO: error, page not given
      //TODO: clean it up, trim, lower
    }

    $this->data['info'] = $this->db->findone('pages', 'id', $pid);
    $this->data['pageid'] = "page-edit";
    $this->twig('page/edit', $this->data);

  }
  // end function edit

  public function new(){
    if ( ! $this->session->loggedin() ) {

      $this->session->addflash('You need to login to edit pages.','notice');
      $this->redirect('user', 'login');

    }

    if ( $this->post['submitted'] ) {
      $page = new Nickyeoman\helpers\pageHelper($this->post);

      if( empty($page->error) ) {

        $id = $this->db->create('pages', $page->page);

      } else {
        dump($page->error);die("there are page errors");
      }

      $this->session->addflash('Saved Page.','notice');
      $this->redirect('page', 'admin');

    }
    //end submitted

    $this->data['info'] = array(
    'title' => 'New Title',
    'slug'  => 'slug',
    'intro' => 'Placeholder Text',
    'body'  => 'Placeholder Text'
    );
    $this->data['mode'] = 'new';
    $this->data['pageid'] = "page-edit";
    $this->session->writeSession();
    $this->twig('page/edit', $this->data);

  }
  //end new

  public function tagadmin($params = null) {

    if ( ! $this->session->loggedin() ) {

      $this->session->addflash("You need to login to edit tags.",'notice');
      $this->redirect('user', 'login');

    } elseif ( !$this->session->inGroup('admin') ) {

      $this->session->addflash("You need Admin permissions to edit tags.",'notice');
      $this->redirect('user', 'login');

    }

    $tag = $params[0];
    if ( empty( $tag ) )
      die("no tag supplied, TODO: 404");

    $result = $this->db->findall('pages', '*', "`tags` LIKE '%$tag%'");
    foreach ($result as $key => $value){
      $value['tags'] = explode(',',$value['tags']);
      $this->data['pages'][$key] = $value;
    }

    $this->data['pageid'] = "page-admin";
    $this->twig('page/admin', $this->data);
  }

  public function createcomment() {

    // Check if logged in
    if ( ! $this->session->loggedin('You need to login to leave a comment.') )
      $this->redirect('user', 'login');

    if ( empty( $this->session->getKey('pageid') ))
      $this->session->addflash("Error: missing pageid.",'error');

    if ( empty( $this->post['comment'] )) {

      $this->session->addflash("You need content in the comment",'error');

    } else {

      // The comment
      // load validation
      $this->valid = new \Nickyeoman\Validation\Validate();
      // Check the comment against spammy words using the validate class
      $badwords = '';
      $badwords = $this->valid->checkSpam($this->post['comment'], $_ENV['SPAMWORDS']);

      if ( !empty($badwords) )
        $this->session->addflash("You can't use these words: $badwords",'error');

    }

    if ( $this->session->flashcount('error') < 1 ) {

      $comment = array(
        'pageid'  => $this->session->getKey('pageid')
        ,'userid' => $this->session->getKey('id')
        ,'body'   => $this->post['comment']
      );

      $id = $this->db->create('comments', $comment);
      $this->session->addflash("Saved Comment.",'notice');

    }

    $this->redirect('page', $this->post['slug']);

  }

  public function admincomment($params = null) {

    if ( ! $this->session->loggedin('You need to login to admin comments.') )
      $this->redirect('user', 'login');

    if ( !$this->session->inGroup('admin', 'You need admin permissions;') )
      $this->redirect('user', 'login');

    // Remove a comment
    if ( !empty($params) ) {
      if ( $params[0] == 'delete' && $params[1] >= 0 ) {
        $this->db->delete('comments', "`id` = $params[1]");
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

    $this->data['comments'] = $this->db->query($SQL);

    $this->data['pageid'] = "comments-admin";
    $this->twig('page/adminComments', $this->data);
  }

}
//end class
