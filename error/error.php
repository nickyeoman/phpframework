<?php
   class errorController extends Nickyeoman\Framework\BaseController {

     public function _404() {

       header('HTTP/1.1 404 Not Found');
       $this->twig('404', $this->data);

     }

   }
