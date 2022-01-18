<?php
class pageController extends Nickyeoman\Framework\BaseController {

  // There is no index page, this will catch
  function override( $params = array() ) {

    // Grab the slug
    $slug = $params[0];

    // Check slug isn't empty
    if ( empty($slug) ) {
      $this->session['error'] = "No Page given.";
			$this->writeSession();
      $this->redirect('error', '_404');
    }

    // Grab page from db
    $this->data['page'] = $this->db->findone('pages', 'slug', $slug);

    // If you don't get one, error
    if ( empty( $this->data['page']['slug'] ) ) {
      $this->session['error'] = "Page not found.";
			$this->writeSession();
      $this->redirect('error', '_404');
    }

    // View
    $this->data['pageid'] = $slug; //css page id
    $this->twig('page/page', $this->data);

  }
  // end override

  // List pages to edit
  function admin() {

    // Check if logged in
    // TODO: anyone can login
    if ( ! $this->session['loggedin'] ) {

      $this->session['notice'] = "You need to login to edit pages.";
      $this->writeSession();
      $this->redirect('user', 'login');

    }

    //Grab pages from db
    $this->data['pages'] = $this->db->findall('pages','id,title,slug');
    // TODO: if empty results

    // View
    $this->data['pageid'] = "page-admin"; //css page id
    $this->twig('page/admin', $this->data);

  }
  //end admin

  // Edit a page
  function edit($params = null) {

    // Check if loggedin
    // TODO: anyone can login
    if ( ! $this->session['loggedin'] ) {

      $this->session['notice'] = "You need to login to edit pages.";
      $this->writeSession();
      $this->redirect('user', 'login');

    }

    // Check if form submitted
    if ( $this->post['submitted'] ) {

      //Create page class (grabs post, cleans data)
      $page = new Nickyeoman\helpers\pageHelper($this->post);

      // store to db
      $id = $this->db->update('pages', $page->page , 'id' );

      //redirect
      $this->session['notice'] = "Saved Page.";
      $this->writeSession();
      $this->redirect('page', 'admin');

    }

    $pid = $params[0];

    if ( empty($pid) ) {
      //TODO: error, page not given
      //TODO: clean slug, trim, lower
    }

    // fetch from db
    $this->data['info'] = $this->db->findone('pages', 'id', $pid);

    // View
    $this->data['pageid'] = "page-edit";
    $this->twig('page/edit', $this->data);

  }
  // end function edit

  /**
  * Creating a new page
  * New pages don't have ids
  **/
  public function new(){

    // TODO: merge with edit above, just add id checking

    // if loggedin
    // TODO: anyone can login
    if ( ! $this->session['loggedin'] ) {

      $this->session['notice'] = "You need to login to edit pages.";
      $this->writeSession();
      $this->redirect('user', 'login');

    }

    if ( $this->post['submitted'] ) {

      $page = new Nickyeoman\helpers\pageHelper($this->post);

      if( empty($page->error) )
        $id = $this->db->create('pages', $page->page);
      else
        dump($page->error);die("there are page errors");

      // redirect
      $this->session['notice'] = "Saved Page.";
      $this->writeSession();
      $this->redirect('page', 'admin');

    }
    //end submitted

    // View
    $this->data['info'] = array(
    'title' => 'New Title',
    'slug' => 'slug',
    'intro' => 'Placeholder Text',
    'body' => 'Placeholder Text'
    );
    $this->data['mode'] = 'new'; //for the twig template (placeholder vs value)
    $this->data['pageid'] = "page-edit"; // css body id

    $this->twig('page/edit', $this->data);

  }
  //end new

}
//end class
