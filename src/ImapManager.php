<?php
namespace Nickyeoman\Framework;
use \Exception;

/**
* IMAP Class
* v0.2 (beta)
**/

class ImapManager {

  public $mailbox = null; //class
  public $mailids = '';
  public $mails = array();

  public function __construct() {

    // Check if the server has imap working (docker should work)
    if(!function_exists('imap_open')){
      throw new Exception('IMAP is not enabled!');
    }


    $this->mailbox = imap_open($_ENV['IMAP_SERVER'], $_ENV['IMAP_USER'], $_ENV['IMAP_PASSWORD']) 
      or die('unable to connect Email: ' . imap_last_error());
    
    // list of mail ids
    $this->mailids = imap_search($this->mailbox, 'ALL'); //https://www.php.net/manual/en/function.imap-search.php
    
  }

  public function disconnect(){
    imap_close($this->mailbox);
  }

  public function getEmails(){

    if ($this->mailids) {

      rsort($this->mailids);

      foreach ($this->mailids as $email_number) {
        $headers = imap_fetch_overview($this->mailbox, $email_number, 0);

        /*  Returns a particular section of the body*/
        $message = imap_fetchbody($this->mailbox, $email_number, '1');
        $structure = imap_fetchstructure($this->mailbox, $email_number);

        // Email body decoding
        // https://www.learn-codes.net/php/php-email-body-decoding/
        if(isset($structure->parts) && is_array($structure->parts) && isset($structure->parts[1])) {
          $part = $structure->parts[1];
          if($part->encoding == 3) {
              $message = imap_base64($message);
          }else if($part->encoding == 1) {
              $message = imap_8bit($message);
          }else{
              $message = imap_qprint($message);
          }
        }

        $this->mails[$email_number] = array(
          'subject' => $headers[0]->subject
          ,'from' => $headers[0]->from
          ,'date' => $headers[0]->date
          ,'body' => utf8_encode($message)
        );

      }

      return $this->mails;
                        
    }



  }
}
