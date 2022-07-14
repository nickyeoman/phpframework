# Sessions

This framework has a build in sessions helper.

It helps the default php session and the base controller should write the session after execution.

## Set Session

You can manually write a session with:
```php
$this->session->writeSessoin();
```

This will write what's in the array $this->session->session to php's $_SESSION;

## Destroy Session

```php
$this->session->destroySession();
```

Will distroy php's $_SESSION, clear $this->session (array in object, not object) and start a new session as required by the framework.

## Session Array

The framework's session array can be accessed through the base controller (in your controllers) like so:

```php
$this->session->getKey('theKey');
```

Framework set keys are as follows

* sessionid - Php's default sessionid
* formkey   - a hash to prevent external forms from posting
* loggedin  - If the current user is loggedin
* usrgrps   - an array of the groups the current user belongs to
* flash     - array of flash messages
* page      - last page visited
* pageid    - this is the last pageid set, if you visit other pages that don't have an id, the one with an id will remain

You can set your own keys:

```php
$this->session->setKey('key','value');
```

This will override the value of the current key.

## User

You can check if a user is logged in with the session.
```php
$this->session->loggedin();
```

This will return a true or false if the user is logged in.
User functions are controlled by the users controller.
Groups and Users can only be checked with sessions.

## Groups

User management in this frameworks makes use of sessions.
Group names can only be in lower case and should only be alphanumeric.

```PHP
$this->session->inGroup('group_to_check','message', false);
```
This only returns if the current user is in the given group (t/f).

The 'message' is the flash error to return to the user if false. Empty for no flash message.
Currently only error messages can be flashed.

## Flash

You can create a flash message for the view like so:

```php
$this->session->addflash('My error message', 'name_of_message');
```
The name_of_message is the array value for the flash session.  
The framework uses notice and error, but you can add any you want.

### clear flash

```php
$this->session->clearflash();
```

Clears the flash array.
