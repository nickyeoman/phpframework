<?php
namespace Nickyeoman\Framework;

class BaseController {

  public $db = null; //class

  /*
  * Base controller
  */
  public function __construct() {
    //nothing here now
  }
  // End construct

  /**
  * Redirect to the correct controller and action (page)
  **/
  public function redirect($controller = 'index', $action = 'index') {

    // set controller
    if ($controller == 'index' || empty($controller) )
      $controller = '/';
    else
      $controller = "/$controller";

    // set method/action
    if ($action == 'index' || empty($action) )
      $action = '';
    else
      $action = "/$action";

    // php redirect
    exit(header("Location: $controller$action"));

  }
  // END redirect

  //Call Twig as a view
  public function twig($viewname = 'index', $vars = array(), $component = null ) {

      //TWIG
      $loader = new \Twig\Loader\FilesystemLoader($_ENV['BASEPATH'] . '/' . $_ENV['VIEWPATH']);
      $loader->addPath($_ENV['BASEPATH'] . '/vendor/nickyeoman/nytwig/src', 'nytwig');

      //load component view
      if ( $component != null ) {

        $loader->prependPath($_ENV['BASEPATH'] . "/vendor/nickyeoman/phpframework/src/Components/$component/twig");
        
      }
      // end if load component view

      $this->twig = new \Twig\Environment($loader, [
          'cache' => $_ENV['BASEPATH'] . '/' .$_ENV['TWIGCACHE'],
          'debug' => true,
      ]);

      $this->twig->addExtension(new \Twig\Extension\DebugExtension());

      echo $this->twig->render("$viewname.html.twig", $vars);

  }
  // End twig

  /**
  * A wrapper for the nette SMTP
  * make sure your ENV is set
  * won't send in debug mode
  * easy to use $this->sendEmail($to,$subject,$body);
  **/
  public function sendEmail($to = '', $subject = '', $body = '') {

    // Check params
    if ( empty($to) || empty($subject) || empty($body) )
      dump('All params required: $this->sendEmail($to,$subject, $body)');
      return false;

    // Check env
    if ($_ENV['DEBUG'] != 'display'){

      // https://packagist.org/packages/nette/mail
      $mail = new \Nette\Mail\Message;

      $mail->setFrom($_ENV['MAIL_FROM_NAME'] . ' <' . $_ENV['MAIL_FROM_ADDRESS'] .'>')
        ->addTo( $to )
        ->setSubject( $subject )
        ->setBody( $body )
      ;

      $mailer = new \Nette\Mail\SmtpMailer([
         'host'     => $_ENV['MAIL_HOST'],
         'username' => $_ENV['MAIL_USERNAME'],
         'password' => $_ENV['MAIL_PASSWORD'],
         'secure'   => $_ENV['MAIL_ENCRYPTION'],
         'port'     => $_ENV['MAIL_PORT'],
       ]);

      $mailer->send($mail);
    }
    // end if debug

  }
  // end sendEmail

  /**
  * Grabs a markdown file
  * Parm1: Path to file from index.php
  * Parm2: key from array to populate default: $this->data['content']
  **/
  public function markdownFile($filename = '', $datakey = 'content') {
    if ( empty($filename) ) {
      die("Error: no filename given to mardownFile");
    }

    $f = fopen($filename, 'r');

    if ($f) {
     $contents = fread($f, filesize($filename));
     fclose($f);

     $Parsedown = new \Parsedown();
     $this->data[$datakey] = $Parsedown->text( $contents );
     return $contents;
   } else {
     die("Error: mardownFile, file not found");
   }

  }
  //end markdownFile

  /**
   * Log to the database
   */
  public function log($level = 'DEBUG', $title = 'Called log', $location = 'Base Controller', $content = "NULL") {
    if ($_ENV['LOGGING'] == 'mysql') {

      //prepare post
      if ( $this->post['submitted'] ) {
        unset($this->post['password']);
        $post = json_encode($this->post);
      } else {
        $post = null;
      }

      $log = array(
        'level'     => strtoupper($level),
        'title'     => $title,
        'content'   => $content,
        'location'  => $location, //location of code
        'ip'        => $this->data['ip'],
        'url'       => $this->data['uri'],
        'session'   => json_encode($this->session),
        'post'      => $post,
        'time'      => 'NOW()'
      );

      foreach ($log as $key => $value) {
        if ( empty($value) ) {
          unset($log[$key]);
        }
      }

      $this->db->create('logs', $log);
    }
    // end mysql
  }
  // end function log

}
// end BaseController
