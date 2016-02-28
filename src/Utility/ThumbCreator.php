<?php
/**
 * This file is part of Thumbs.
 *
 * Thumbs is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Thumbs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Thumbs.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace Thumbs\Utility;

use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;

/**
 * Utility to create a thumb.
 * 
 * Please, refer to the `README` file to know how to use the utility and to see examples.
 */
class ThumbCreator {
	/**
	 * Imagick object
	 * @var object 
	 */
	protected $imagick;
	
	/**
	 * Height of the origin file
	 * @var int
	 */
	protected $height;

	/**
	 * Origin file path
	 * @var string
	 * @see origin() 
	 */
	protected $origin;
	
	/**
	 * Target file path
	 * @var string
	 * @see target() 
	 */
	protected $target;
	
	/**
	 * Width of the origin file
	 * @var int
	 */
	protected $width;

	/**
	 * Construct. 
	 * 
	 * Sets the origin file, if passed. Otherwise, you have to call the `origin()` method
	 * @param string $origin Origin file path
	 * @return \Thumbs\Utility\ThumbCreator
	 * @throws InternalErrorException
	 * @uses origin()
	 */
	public function __construct($origin = NULL) {
		//Checks for Imagick extension
        if(!extension_loaded('imagick'))
            throw new InternalErrorException(__d('thumb', '{0} is not available', 'Imagick'));
		
		if(!empty($origin))
			$this->origin($origin);
		
		return $this;
	}
	
	/**
	 * Destruct
	 * @uses $imagick
	 * @uses $origin
	 */
	public function __destruct() {
		//Removes the temporary file, if exists
		if(dirname($this->origin) === sys_get_temp_dir())
			unlink($this->origin);
		
		//Clears all resources associated to Imagick object
		if(!empty($this->imagick))
			$this->imagick->clear();
	}
	
	/**
	 * Downloads a file as a temporary file. This is useful if the source file is a remote file
	 * @param string $url File url
	 * @return string Temporary file path
	 * @throws NotFoundException
	 */
	protected function _downloadTemporary($url) {
		//Checks if the file is readable
		if(!$fopen = @fopen($url, 'r'))
			throw new NotFoundException(__d('thumb', 'File or directory {0} not readable', $url));
		
		//Downloads as temporary file
		$tmp = sprintf('%s.%s', tempnam(sys_get_temp_dir(), md5($url)), extension($url));
		
		file_put_contents($tmp, $fopen);
		
		return $tmp;
	}
	
	/**
	 * Sets the origin file
	 * @param string $origin Origin file path
	 * @return \Thumbs\Utility\ThumbCreator
	 * @throws InternalErrorException
	 * @uses _downloadTemporary()
	 * @uses $height
	 * @uses $imagick
	 * @uses $origin
	 * @uses $width
	 */
	public function origin($origin) {
		//If the origin file is a remote file, downloads as temporary file
		if(is_url($origin))
			$origin = $this->_downloadTemporary($origin);
		//Else, if it's a relative path, the file will be relative to `APP/webroot/img`
		elseif(!\Cake\Filesystem\Folder::isAbsolute($origin))
			$origin = WWW_ROOT.'img'.DS.$origin;
		
		//Checks if the origin file is readable
		if(!is_readable($origin))
			throw new InternalErrorException(__d('thumb', 'File or directory {0} not readable', $origin));
				
		//Checks if the origin is an image
		if(!in_array(extension($origin), ['gif', 'jpg', 'jpeg', 'png']))
            throw new InternalErrorException(__d('thumb', 'The file {0} is not an image', $origin));
		
		//Creates the Imagick object adn strips all profiles and comments
		$this->imagick = new \Imagick($origin);
		$this->imagick->stripImage();
		
		//For jpeg images, sets the image compression
		if(mime_content_type($origin) === 'image/jpeg') {
			$this->imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
			$this->imagick->setImageCompressionQuality(100);
		}
		
		//Sets the origin file and the origin size
		$this->origin = $origin;
		$this->height = $this->imagick->getimageheight();
		$this->width = $this->imagick->getImageWidth();
		
		return $this;
	}
	
	/**
	 * Resizes an image
	 * @param int $width Final width
	 * @param int $height Finali height
	 * @return \Thumbs\Utility\ThumbCreator
	 * @throws InternalErrorException
	 * @uses $imagick
	 * @uses $height
	 * @uses $target
	 * @uses $width
	 */
	public function resize($width = 0, $height = 0) {
		//Checks for target
		if(empty($this->target))
			throw new InternalErrorException(__d('thumb', 'The target file has not been set'));
		
		//Checks for final size
		if(empty($width) && empty($height))
			throw new InternalErrorException(__d('thumb', 'The final size are missing'));
		
		//Checks for size
		if(($width && $width >= $this->width) || ($height && $height >= $this->height))
			throw new InternalErrorException(__d('thumb', 'The required size exceed the original size'));			
				
		//Writes the thumbnail
		$this->imagick->thumbnailImage($width, $height, $width && $height);
		$this->imagick->writeImage($this->target);
		
		return $this;
	}
	
	/**
	 * Sets the target file
	 * @param string $target Target file path
	 * @return \Thumbs\Utility\ThumbCreator
	 * @throws InternalErrorException
	 * @uses $target
	 */
	public function target($target) {
		//Checks if the target file already exists
		if(file_exists($target))
			throw new InternalErrorException(__d('thumb', 'File or directory {0} already exists', $target));
		
		//Checks if the target directory is writable
		if(!is_writable(dirname($target)))
			throw new InternalErrorException(__d('thumb', 'File or directory {0} not writeable', dirname($target)));
		
		$this->target = $target;
		
		return $this;
	}
}

