<?php
namespace Nickyeoman\Framework\Helpers;

USE \Nickyeoman\Validation;

class pageHelper {

  public  $post = array();
  public  $page = array(
    'id'      => '',
    'title'   => '',
    'slug'    => '',
    'intro'   => '',
    'body'    => '',
    'heading' => '',
    'description' => '',
    'keywords' => '',
    'author' => '',
    'created' => 'NOW()',
    'updated' => 'NOW()',
    'draft' => '',
    'path' => '',
    'notes' => ''
  );
  public $tags = '';
  public $error = array();

  /*
  * Create the Page object
  */
  public function __construct($post) {

    //Save the original post data
    $this->post = $post;

    $this->set_id();
    $this->set_title();
    $this->set_heading();
    $this->set_slug();
    $this->set_intro();
    $this->set_body();
    $this->set_description();
    $this->set_keywords();
    $this->set_author();
    $this->set_draft();
    $this->set_path();
    $this->set_notes();
    $this->set_tags();

  }
  // End Construct

  public function set_id($id = null ) {

    if ( !empty( $this->post['id'] ) )
      $id = $this->post['id'];

    if ( empty($id) )
      unset($this->page['id']);
    elseif( ! is_numeric($id) )
      unset($this->page['id']);

    $this->page['id'] = $id;

  }

  public function set_title( $title = null ) {

    if ( !empty( $_POST['title'] ) )
      $title = $_POST['title'];

    trim($title);
    // is the title empty?
    if ( empty($title) ) {

      //if so check if the heading is also
      if ( empty( $_POST['heading']) ) {

        $this->addError('Missing Title');
        return false;

      } else {

        $title = trim($_POST['heading']);

      }

    }

    $this->page['title'] = str_replace("'",'&#39;',$title);

  }

  public function set_heading( $heading = null ) {

    if ( !empty( $_POST['heading'] ) )
      $heading = $_POST['heading'];

    if ( !empty($heading) )
      trim($heading);

    // is the title empty?
    if ( empty($heading) ) {

      //if so check if the title is also
      if ( empty( $_POST['title']) ) {

        $this->addError('Missing Heading');
        return false;

      } else {

        $heading = trim($_POST['title']);

      }

    }

    // clean for database
    $this->page['heading'] = str_replace("'",'&#39;',$heading);

  }

  public function set_slug( $slug = null ) {

    if ( !empty( $this->post['slug'] ) )
      $slug = $this->post['slug'];

    if ( !empty($slug) )
      trim($slug);

    $this->page['slug'] = $slug;
    return $slug;

  }

  public function set_path( $path = null ) {

    if ( !empty( $this->post['path'] ) )
      $path = $this->post['path'];

    if ( !empty($path) )
      trim($path);

    $this->page['path'] = $path;
    return $path;

  }

  public function set_notes( $notes = null ) {

    if ( !empty( $this->post['notes'] ) )
      $notes = $this->post['notes'];

    if ( !empty($notes) )
      trim($notes);

    $this->page['notes'] = $notes;
    return $notes;

  }


  public function set_tags( $tags = null ) {

    if ( !empty( $this->post['tags'] ) ) {
      $tagArr = explode(',',trim($this->post['tags']));

      $cleanArr = array();
      foreach ($tagArr as $key => $value) {
        $value = trim($value);  //remove whitespace
        $value = preg_replace('/\s+/', ' ', $value); //remove double spaces
        $value = preg_replace("![^a-z0-9]+!i", "-", $value);//all special characters to dash
        $value = strtolower($value);//lowercase
        $cleanArr[$key] = $value;
      }

      //$tags = implode(',',$cleanArr);

    }

    $this->tags = $cleanArr;
    return $tags;

  }

  public function set_intro( $intro = null ) {

    // We want the HTML so we are just going to grap the $_POST
    if ( !empty( $_POST['intro'] ) ) {

      $intro = $this->post['intro'];
      $intro = trim($intro);
      $intro = str_replace("'",'&#39;',$intro);
      $this->page['intro'] = $intro;

    } else {
      $this->page['intro'] = "";
    }

  }

  public function set_body( $body = null ) {

    // We want the HTML so we are just going to grap the $_POST
    if ( !empty( $_POST['body'] ) )
      $body = $_POST['body'];

    $body = trim($body);
    $body = str_replace("'",'&#39;',$body);
    $this->page['body'] = $body;

  }

  public function set_description( $description = null ) {

    if ( !empty( $this->post['description'] ) )
      $description = $this->post['description'];

    trim($description);
    $this->page['description'] = $description;
    return $description;

  }

  public function set_keywords( $keywords = null ) {

    if ( !empty( $this->post['keywords'] ) )
      $keywords = $this->post['keywords'];

    trim($keywords);
    $this->page['keywords'] = $keywords;
    return $keywords;

  }

  public function set_author( $author = null ) {

    if ( !empty( $this->post['author'] ) )
      $author = $this->post['author'];

    trim($author);
    $this->page['author'] = $author;
    return $author;

  }

  public function set_draft( $draft = null ) {

    if ( !empty( $this->post['draft'] ) )
      $draft = $this->post['draft'];

    if ( $draft == "on" )
      $draft = '1';
    else
      $draft = '0';

    $this->page['draft'] = $draft;
    return $draft;

  }

  private function addError($errorMessage = 'Error Thrown - Page Object Helper') {
    array_push($this->error, $errorMessage);
  }

}
