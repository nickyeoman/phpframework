#!/bin/bash

ControllerName=$1;

cat << EOF > controllers/${ControllerName}.php
<?php
   class ${ControllerName}Controller extends Nickyeoman\Framework\BaseController {

     function index() {

       $data = array( ['templatevar1' => 'title page' ] );
       $this->twig('index', $data);

     }
   }
EOF


cat << EOF > views/${ControllerName}.html.twig
  {% set title = 'Title of Page' %}
  {% extends "layout/master.html.twig" %}

  {% block content %}
  <p>Sample Page Content.</p>
  {% endblock %}
EOF
