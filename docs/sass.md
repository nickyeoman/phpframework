# SASS

## Using SASS with phpframework

When pulling phpframework from composer, it includes the [nickyeoman/sassLibrary](https://github.com/nickyeoman/sassLibrary) as well.

```bash
# Check sassLibrary is installed
composer show -- nickyeoman/sassLibrary
```

SASS needs to be compiled to work, so have to run sass: sass sass/project.sass public/css/main.css

The default template already looks for main.css

Then you can modify your project as you normally would for sass.

[Lear more about nickyeoman/sassLibrary](https://github.com/nickyeoman/sassLibrary)

### Development SASS

The watch command for live updates: sass --watch sass/master.sass public/css/main.css

### Install SASS on Ubuntu

First you need npm:
```bash
sudo apt install npm
```

Then you need sass:
```bash
sudo npm install -g sass
```

See [SASS' website for more](https://sass-lang.com/install)
