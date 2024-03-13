<?php
namespace Nickyeoman\Framework\Controllers\Pages;

use Nickyeoman\Framework\Classes\BaseController;
USE Nickyeoman\Dbhelper\Dbhelp as DB;
use Nickyeoman\Framework\Attributes\Route;
USE Nickyeoman\Validation\Validate as Validate;

// Front End Pages Controller
class Front extends BaseController {

  #[Route('/page/{slug}')]  
  function page( $slug ) {

    $s = $this->session;
		$v = $this->viewClass;
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
      $this->view('@cms/error/404');
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
    $this->view('@cms/pages/page');

  }
  // end page

  // TODO: Store Username
  #[Route('/page/createcomment', methods: ['POST'])]
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
      $valid = new Validate();
      // Check the comment against spammy words using the validate class
      $badwords = '';
      $spamwords = ''; // TODO: grab from database
      $badwords = $valid->checkSpam($r->post['comment'], $spamwords);

      if ( !empty($badwords) )
        $s->addFlash("You can't use these words: $badwords",'error');

    }

    if ( $s->flashcount('error') < 1 ) {

      $comment = array(
        'pageid'  => $s->getKey('pageid')
        ,'userid' => $s->getKey('id')
        ,'body'   => $r->get('comment')
      );

      $DB = new DB();
      $id = $DB->create('comments', $comment);
      $DB->close();
      $s->addFlash("Saved Comment.",'notice');

    }

    redirect('/page/' . $r->post['slug']);

  }

} //end class
