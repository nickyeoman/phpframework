<?php namespace Nickyeoman\Framework\Classes;
/**
* Request Class
* v1.0
**/

class RequestManager {
    
    public $submitted = false;
    public $post = array();

    public function __construct($Session, $ViewData) {
        
        //bdump($_POST, '_POST');
        
        if ( ! empty( $_POST['formkey'] ) ){

            //check session matches
            if ( $_POST['formkey'] == $Session->getKey('formkey') ) {
                
                $this->submitted = true;

                foreach( $_POST as $key => $value){

                    //clean variable TODO: might not want this all the time (html)
                    $prep = trim(strip_tags($value));
                    $this->post[$key] = htmlentities($prep, ENT_QUOTES);

                }

            } else {

                //error no form key.
                $Session->addFlash( 'There was a problem with the session, try again', 'error' );

            }

        } //end if

        $pa = $this->post; //post array
        unset($pa['formkey']);
        $ViewData->setPost($pa);

    } // end construct

    public function get($key) {

        if( !empty($this->post[$key]) )
            return $this->post[$key];
        else
            return false;
        
    }

}