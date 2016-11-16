Starting november 2016, **this repository has been abandoned**.  
See instead: [cakephp-thumber](https://github.com/mirko-pagliai/cakephp-thumber).

***

# Thumbs
*Thumbs* is a CakePHP plugin to create thumbnails.  
It can create thumbnails (rectangular or square) of local and remote images.

## Installation
You can install the plugin via composer:

    $ composer require --prefer-dist mirko-pagliai/thumbs
    
You have to edit `APP/config/bootstrap.php` to load the plugin:

    Plugin::load('Thumbs', ['bootstrap' => TRUE, 'routes' => TRUE]);
    
By default the plugin uses the `APP/tmp/thumbs` directory to create 
thumbnails.  
So you have to create the directory and make it writable:

    $ mkdir backup && chmod 775 tmp/thumbs/

You can change this directory by defining the `THUMBS` constant until the 
plugin is loaded.  
Example:

    define('THUMBS', 'alternative'.DS.'directory'.DS.'for'.DS.'thumbnails);
    Plugin::load('Thumbs', ['bootstrap' => TRUE, 'routes' => TRUE]);

## How to create thumbnails
Simply use the `ThumbHelper`, after you have 
[loaded it](http://book.cakephp.org/3.0/en/views/helpers.html#configuring-helpers).

Basically the helper provides two methods, `resize()` (for rectangular 
thumbnails) and  `square()` (for square thumbnails).

### `resize()` and `square()` methods

The `resize()` method takes `height` and/or `width` options. Example:

    echo $this->Thumb->resize('logo.png', ['width' => 100]);

This will create and display a thumbnail of the `APP/webroot/img/logo.png` 
file, with a maximum width of 100 pixels.  
Note that if the file path is a local file and if it's relative, it will be 
relative to `APP/webroot/img`.

You can also use full path. Example:

    echo $this->Thumb->resize('/my/full/path/image.jpg', ['height' => 200, 'width' => 100]);

This will create and display a thumbnail of the `/my/full/path/image.jpg` file, 
with a maximum height of 200 pixel and a maximum width of 100 pixels.

You can also use remote file. Example:

    echo $this->Thumb->resize('www.mysite.com/example.gif', ['width' => 100]);
    
This will create and display a thumbnail of the `www.mysite.com/example.gif` 
file, with a maximum width of 100 pixels.

Instead, the `square()` method creates a square thumbnail and takes the `side` 
option, indicating the length of the side. Example:

    echo $this->Thumb->square('logo.png', ['side' => 100]);
    
This will create and display a square thumbnail of the 
`APP/webroot/img/logo.png` file, with sides of 100 pixels.   
Note that for square thumbnails, the thumbnail will start from the center of 
the image.

Both methods also accept the `force` option (boolean values), which forces the 
thumbnail sizes larger than the origin image. Example:

    echo $this->Thumb->resize('logo.png', ['force' => true, 'width' => 500]);
    
This will create and display a thumbnail of the `APP/webroot/img/logo.png` 
file, with a width of 500 pixels, **even if the original image is smaller**.

### Other methods
The helper also provides `resizeUrl()` and `squareUrl()` methods, that simply 
return the thumbnails url.

The `image()` and `url()` methods are magic methods that, based on the options 
given, call the other methods. Example:

    echo $this->Thumb->image('logo.png', ['width' => 100]);
    
will call the `resize()` method, because there's the `width` option.

## Versioning
For transparency and insight into our release cycle and to maintain backward 
compatibility, *Thumbs* will be maintained under the 
[Semantic Versioning guidelines](http://semver.org).
