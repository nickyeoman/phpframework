#!/bin/bash

ControllerName=$1;

# Create child controller
cat << EOF > controllers/${ControllerName}.php
<?php
   namespace Nickyeoman\Framework\Controller;
   class ${ControllerName}Controller extends Nickyeoman\Framework\BaseController {

     function index() {

       \$this->twig('${ControllerName}', \$this->data);

     }
   }
EOF

# Create view
cat << EOF > views/${ControllerName}.html.twig
  {% set title = '${ControllerName} Page' %}
  {% extends "@nytwig/master.html.twig" %}

  {% block content %}
  <p>Sample ${ControllerName} Page Content.</p>
  {% endblock %}
EOF
