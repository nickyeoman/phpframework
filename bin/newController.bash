#!/bin/bash

ControllerName=$1;

# Create child controller
cat << EOF > controllers/${ControllerName}.php
<?php
   class ${ControllerName}Controller extends Nickyeoman\Framework\BaseController {

     function index() {

       \$data = array();
       \$data['notice'] = \$this->readFlash('notice'); // Flash Data for view
       \$this->twig('${ControllerName}', \$data);

     }
   }
EOF

# Create view
cat << EOF > views/${ControllerName}.html.twig
  {% set title = '${ControllerName} Page' %}
  {% extends "layout/master.html.twig" %}

  {% block content %}
  <p>Sample ${ControllerName} Page Content.</p>
  {% endblock %}
EOF

