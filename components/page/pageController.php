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

    $this->data['page'] = $pagedata;
    $this->data['pageid'] = $slug;
    $this->twig('page', $this->data);

  }
  // end override

  // List pages to edit
  function admin() {

    if ( ! $this->session['loggedin'] ) {

      $this->session['notice'] = "You need to login to edit pages.";
      $this->writeSession();
      $this->redirect('user', 'login');

    } elseif ( !$this->session['NY_FRAMEWORK_USER']['admin'] ) {

      $this->session['notice'] = "You need Admin permissions to edit pages.";
      $this->writeSession();
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
    $this->twig('admin', $this->data);

  }
  //end admin

  // Edit a page
  function edit($params = null) {

    if ( ! $this->session['loggedin'] ) {

      $this->session['notice'] = "You need to login to edit pages.";
      $this->writeSession();
      $this->redirect('user', 'login');

    } elseif ( !$this->session['NY_FRAMEWORK_USER']['admin'] ) {

      $this->session['notice'] = "You need Admin permissions to edit pages.";
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
    $this->twig('edit', $this->data);

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

    $this->twig('edit', $this->data);

  }
  //end new

  public function tagadmin($params = null) {
    $tag = $params[0];
    if ( empty( $tag ) )
      die("no tag supplied, TODO: 404");

    $result = $this->db->findall('pages', '*', "`tags` LIKE '%$tag%'");
    foreach ($result as $key => $value){
      $value['tags'] = explode(',',$value['tags']);
      $this->data['pages'][$key] = $value;
    }

    $this->data['pageid'] = "page-admin";
    $this->twig('admin', $this->data);
  }

  public function sitemap() {

    // xml for the sitemap
    // https://www.sitemaps.org/protocol.html
    header('Content-Type: text/xml');

    //Get the pages (priority of zero will remove it from the sitemap, drafts are not shown)
    $thepages = $this->db->findall('pages', 'draft, slug, updated, changefreq, priority', 'priority > 0 AND draft < 1', 'updated');

    // Start the view
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    // Loop through the pages
    foreach( $thepages as $v) {

      $date = new DateTime($v['updated']);
      $date = $date->format('Y-m-d');

      echo "<url>";
      echo "<loc>" . $_ENV['BASEURL'] . '/page/' . $v['slug'] . "</loc>";
      echo "<lastmod>" . $date . "</lastmod>";
      echo "<changefreq>" . $v['changefreq'] . "</changefreq>";
      echo "<priority>" . $v['priority'] . "</priority>";
      echo "</url>";
    }

    echo "</urlset>";

    // TODO: add caching
  }

}
//end class
