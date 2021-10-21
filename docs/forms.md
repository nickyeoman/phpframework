# Web Forms

Creating a form is as follows:

## Step 1 - Create the controller/view

```bash
bash vendor/nickyeoman/phpframework/bin/newController.bash contact
```

## Step 2 - Edit the view

Make sure you have the formkey from the session.  
The submit action is whatever you choose, it should be the same page if you want to direct error output and maintain what was entered in the form.

```html
{% set title = 'Contact Us' %}
{% extends "layout/master.html.twig" %}

{% block content %}
<h1>Contact Us</h1>

<div class="formContainer">
  <form action="/contact" method="post">
    <input type="hidden" name="formkey" value="{{ formkey }}" />

    <input type="text" placeholder="Your Email address" name="email" value="{{ email }}" required>

    <textarea name="message" rows="4" cols="50">
      Enter your message here.
    </textarea>

    <input type="submit" value="Send Message" name="submit" />

  </form>
</div>
{% endblock %}
```

## Step 3 - The controller requirements

The base controller will find the POST data and place it into $this->post

You need to do a number of things to get the form to work.

### Form Requirement 1 - formkey

The base controller grabs a formkey from the session to combat xss.

```php
$data['formkey'] = $this->session['formkey'];
```

### Form Requirement 2 - submitted

```php
$this->post['submitted']
```
If true, there is post data and the formkey did match.
You can access the post data with $this->post['name'].

### Form Requirement 3 - session

Remember to write your session at the end of your controller.
This is required for formkey to work.

```php
$this->writeSession();
```

### Step 4 - Your controller logic

```php
if ($this->post['submitted']) {

  // sample check email


}
```

And then finish the work based on your project.
