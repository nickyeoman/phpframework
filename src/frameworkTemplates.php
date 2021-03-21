<?php
namespace Nickyeoman\Framework;

/**
 *
 */
class frameworkTemplates {

  function __construct()
  {
    // code...
  }

  function echoControllerTemplate ($controller = 'index', $action = 'index') {
    $template =<<<EOPHP
    <h1>Error Controller does not exist</h1>
    <p>You could create this controller:</p>
    <pre style="border: 1px solid black;background-color:#ddd">
    &lt;?php
    class {$controller}Controller extends Nickyeoman\Framework\BaseController {

    	function {$action}() {

        &#36;data = array( ['templatevar1' => 'title page' ] );
        &#36;this->twig('{$action}', &#36;data);

      }
    }
    </pre>
EOPHP;

    return $template;
  }

  function echoViewTemplate () {
    $template =<<<EOPHP
    <h1>Error View does not exist</h1>
    <p>You could create this view (assuming you have a layout set):</p>
    <pre style="border: 1px solid black;background-color:#ddd">
    {% set title = 'Title of Page' %}
    {% extends "layout/master.html.twig" %}

    {% block content %}
    &lt;p&gt;Sample Page Content.&lt;/p&gt;
    {% endblock %}
    </pre>
EOPHP;

    return $template;
  }
}
