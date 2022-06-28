<?php
namespace Nickyeoman\Framework;

/**
* IMAP Class
* v0.1 (beta)
*
* Just a helper for barbushin/php-imap
**/

class ImapManager {

  public $mailbox = new stdClass();
  public $mailids = '';

  public function __construct() {

    $this->mailbox = new PhpImap\Mailbox($_ENV['IMAP_SERVER'],$env['IMAP_USER'],$env['IMAP_PASSWORD']);
    $this->mailbox->setAttachmentsIgnore(true);

  }

  public function disconnect(){
    $this->mailbox->disconnect();
  }

  public function getEmails(){

    try {
    	// Search in mailbox folder for specific emails
    	// PHP.net imap_search criteria: http://php.net/manual/en/function.imap-search.php
    	// Here, we search for "all" emails
    	$this->mailids = $mailbox->searchMailbox('ALL');
    } catch(PhpImap\Exceptions\ConnectionException $ex) {
    	echo "IMAP connection failed: " . $ex;
    	die();
    }

    dump($this->mailids);

  }
}
