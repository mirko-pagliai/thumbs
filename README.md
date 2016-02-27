# Thumbs
*Thumbs* is a CakePHP plugin to create thumbnails.

## Installation
You can install the plugin via composer:

    $ composer require --prefer-dist mirko-pagliai/thumbs
    
Then, edit `APP/config/bootstrap.php` to load the plugin:

    Plugin::load('Thumbs', ['bootstrap' => TRUE, 'routes' => TRUE]);
    
By default the plugin uses the `APP/tmp/thumbs` directory to create thumbnails.  
So you have to create the directory and make it writable:

    $ mkdir backup && chmod 775 tmp/thumbs/

You can change this directory by defining the `THUMBS` constant until the plugin is loaded. For example:

    define('THUMBS', 'alternative'.DS.'directory'.DS.'for'.DS.'thumbnails);
    Plugin::load('Thumbs', ['bootstrap' => TRUE, 'routes' => TRUE]);

## Versioning
For transparency and insight into our release cycle and to maintain backward compatibility, 
MeTools will be maintained under the [Semantic Versioning guidelines](http://semver.org).
