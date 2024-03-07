#!/bin/bash

ControllerName="$1";

# Check if ControllerName is provided
if [ -z "$ControllerName" ]; then
  echo "Usage: $0 <ControllerName>"
  exit 1
fi

# Create child controller
cat << EOF > App/Controllers/${ControllerName}.php
<?php
namespace App\Controllers;

use Nickyeoman\Framework\Classes\BaseController;
use Nickyeoman\Framework\Attributes\Route;

class ${ControllerName} extends BaseController {

    #[Route('/${ControllerName}')]
    public function index() {
        \$this->view('index');
    }
}
EOF

# Create view
cat << EOF > App/Views/${ControllerName}.html.twig
{% set title = '${ControllerName} Page' %}
{% extends "@nytwig/master.html.twig" %}

{% block content %}
<p>Sample ${ControllerName} Page Content.</p>
{% endblock %}
EOF
