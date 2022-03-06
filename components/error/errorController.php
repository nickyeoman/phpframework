<?php
   class errorController extends Nickyeoman\Framework\BaseController {

     public function _404() {

       $this->log('error','404', '404 Controller - error.php', '404 page' );
       header('HTTP/1.1 404 Not Found');
       $this->twig('404', $this->data);

     }

   }
