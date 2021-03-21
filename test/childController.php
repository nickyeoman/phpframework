<?php
class indexController extends Nickyeoman\Framework\BaseController {

	function index() {

    $data = array( ['templatevar1' => 'title page' ] );
    $this->twig('index', $data);

  }
}
