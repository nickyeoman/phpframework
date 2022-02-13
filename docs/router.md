# Router

The job of this router is to determine what controller to run.
In short it takes the REQUEST_URI and finds the class and method to run.

## How it works

Here is a detailed breakdown of what this router actually does.

First we take the $_SERVER['REQUEST_URI'] and separate the parts.
Best case scenario is /class/method/parm1/parm2

The controller 'class' will be called (if exists) and method(), function inside of that class (if exists).
The parm1 and parm2 will be handed as an array to the function

but obviously it's not always that simple.

### Index page

Let's say we are going to example.com/
There are no parameters here so the router will automatically supply the default 'index' for both.
example.com/ == example.com/index/index

This will also work if just a class is supplied.

example.com/contact == example.com/contact/index

### Controller Names

Controller file names must match the class name before 'Controller';

Class indexController must be in index.php in the controllers directory.

class and file names are case insensitive. iNdEx.php = index.php = INDEX.php

### Overrides

Sometimes you don't want to supply method and want your second URI segment to be a parameter:

example.com/person/name

where person is the class and name is the parameter.

instead of doing something like /person/person/name
you can create an override method in the class:

```php
class personController extends Nickyeoman\Framework\BaseController {
  public function override( $params = array() ) {
    //do work
  }
}
```

Note, the router will check for a method first, so if you have a function nicholas()
/person/nicholas may not give the intended results but things like /person/save will.

If there is a better way to do this, let me know.

### Components

As this framework has grown to more of a CMS there are components included in the vendor/components directory that will load automatically.
To stop the loading of all base components put USECMS=no in the dotenv.

### Error Controller

If the controller is not found, the 'error' controller is called with action _404.
