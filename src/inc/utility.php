<?php
/******************************************
* The utility file is for Global functions
*******************************************/

/**
 * Redirect to the correct controller and action (page)
**/
function redirect($controller = 'index', $action = 'index') {

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

/**
 * A wrapper for the nette SMTP
* make sure your ENV is set
* won't send in debug mode
* easy to use $this->sendEmail($to,$subject,$body);
**/
function sendEmail($to = '', $subject = '', $body = '') {

    // Check params
    if ( empty($to) || empty($subject) || empty($body) ) {
        dump('All params required: $this->sendEmail($to,$subject, $body)');
        return false;
    }

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
function markdownFile($filename = '', $datakey = 'content') {
    if ( empty($filename) )
        die("Error: no filename given to mardownFile");

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
