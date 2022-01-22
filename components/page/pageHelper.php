<?php
namespace Nickyeoman\helpers;
USE \Nickyeoman\Validation;

class pageHelper {

  public  $post = array();
  public  $page = array(
    'id'      => '',
    'title'   => '',
    'slug'    => '',
    'tags'    => '',
    'intro'   => '',
    'body'    => '',
    'heading' => '',
    'description' => '',
    'keywords' => '',
    'author' => '',
    'created' => 'NOW()',
    'updated' => 'NOW()',
    'draft' => ''
  );
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
    $this->set_tags();
    $this->set_intro();
    $this->set_body();
    $this->set_description();
    $this->set_keywords();
    $this->set_author();
    $this->set_draft();

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

    if ( !empty( $this->post['title'] ) )
      $title = $this->post['title'];

    trim($title);
    // is the title empty?
    if ( empty($title) ) {

      //if so check if the heading is also
      if ( empty( $this->post['heading']) ) {

        $this->addError('Missing Title');
        return false;

      } else {

        $this->page['title'] = trim($this->post['heading']);

      }

    } else {

      $this->page['title'] = $title;

    }

    return $this->page['title'];

  }

  public function set_heading( $heading = null ) {

    if ( !empty( $this->post['heading'] ) )
      $title = $this->post['heading'];

    if ( !empty($heading) )
      trim($heading);

    // is the title empty?
    if ( empty($heading) ) {

      //if so check if the title is also
      if ( empty( $this->post['title']) ) {

        $this->addError('Missing Heading');
        return false;

      } else {

        $this->page['heading'] = trim($this->post['title']);

      }

    } else {

      $this->page['heading'] = $heading;

    }

    return $this->page['heading'];

  }

  public function set_slug( $slug = null ) {

    if ( !empty( $this->post['slug'] ) )
      $slug = $this->post['slug'];

    if ( !empty($slug) )
      trim($slug);

    $this->page['slug'] = $slug;
    return $slug;

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

      $tags = implode(',',$cleanArr);

    }

    $this->page['tags'] = $tags;
    return $tags;

  }

  public function set_intro( $intro = null ) {

    if ( !empty( $this->post['intro'] ) )
      $intro = $this->post['intro'];

    trim($intro);
    $this->page['intro'] = $intro;
    return $intro;

  }

  public function set_body( $body = null ) {

    // We want the HTML so we are just going to grap the $_POST
    if ( !empty( $_POST['body'] ) )
      $body = $_POST['body'];

    trim($body);
    $this->page['body'] = $body;
    return $body;

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
