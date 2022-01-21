<?php
class pageController extends Nickyeoman\Framework\BaseController {

  // There is no index page, this will catch
  function override( $params = array() ) {

    //TODO: 404 if empty
    $slug = $params[0];
    $this->data['page'] = $this->db->findone('pages', 'slug', $slug);

    if ( empty($this->data['page'])) {
      header('HTTP/1.1 404 Not Found');
      $this->twig('404', $this->data);
      die();
    }
    $this->data['pageid'] = $slug;
    $this->twig('page/page', $this->data);

  }
  // end override

  // List pages to edit
  function admin() {

    if ( ! $this->session['loggedin'] ) {

      $this->session['notice'] = "You need to login to edit pages.";
      $this->writeSession();
      $this->redirect('user', 'login');

    }

    //Grab pages
    $this->data['pages'] = $this->db->findall('pages','id,title,slug');
    $this->data['pageid'] = "page-admin";
    $this->twig('page/admin', $this->data);

  }
  //end admin

  // Edit a page
  function edit($params = null) {

    if ( ! $this->session['loggedin'] ) {

      $this->session['notice'] = "You need to login to edit pages.";
      $this->writeSession();
      $this->redirect('user', 'login');

    }

    if ( $this->post['submitted'] ) {
      $page = new Nickyeoman\helpers\pageHelper($this->post);

      $id = $this->db->update('pages', $page->page , 'id' );

      $this->session['notice'] = "Saved Page.";
      $this->writeSession();
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
    if ( ! $this->session['loggedin'] ) {

      $this->session['notice'] = "You need to login to edit pages.";
      $this->writeSession();
      $this->redirect('user', 'login');

    }

    if ( $this->post['submitted'] ) {
      $page = new Nickyeoman\helpers\pageHelper($this->post);

      if( empty($page->error) ) {

        $id = $this->db->create('pages', $page->page);

      } else {
        dump($page->error);die("there are page errors");
      }


      $this->session['notice'] = "Saved Page.";
      $this->writeSession();
      $this->redirect('page', 'admin');

    }
    //end submitted

    $this->data['info'] = array(
    'title' => 'New Title',
    'slug' => 'slug',
    'intro' => 'Placeholder Text',
    'body' => 'Placeholder Text'
    );
    $this->data['mode'] = 'new';
    $this->data['pageid'] = "page-edit";

    $this->twig('page/edit', $this->data);

  }
  //end new

}
//end class
