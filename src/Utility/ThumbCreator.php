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
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace Thumbs\Utility;

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;

/**
 * Utility to create a thumb.
 *
 * Please, refer to the `README` file to know how to use the utility and to
 * see examples.
 */
class ThumbCreator
{
    /**
     * Height of the origin file
     * @var int
     */
    protected $height;

    /**
     * Path of the origin file
     * @var string
     */
    protected $origin;

    /**
     * Path of the target file
     * @var string
     */
    protected $target;

    /**
     * Marker. It shows whether it is a temporary file
     * @var bool
     */
    protected $temporary = false;

    /**
     * Width of the origin file
     * @var int
     */
    protected $width;

    /**
     * Construct.
     * It sets the origin file.
     *
     * If the origin is relative, it will be relative to  `APP/webroot/img`.
     * @param string $origin Origin file
     * @return \Thumbs\Utility\ThumbCreator
     * @throws InternalErrorException
     * @uses $height
     * @uses $origin
     * @uses $width
     * @uses _downloadTemporary()
     */
    public function __construct($origin)
    {
        //If the origin is a remote file, downloads as temporary file
        if (isUrl($origin)) {
            $origin = $this->_downloadTemporary($origin);
        //If the origin is a local file and if it's relative, it will be
        //  relative to `APP/webroot/img`
        } elseif (!Folder::isAbsolute($origin)) {
            $origin = WWW_ROOT . 'img' . DS . $origin;
        }

        //Checks if the file is readable
        if (!is_readable($origin)) {
            throw new NotFoundException(__d('thumbs', 'File or directory {0} not readable', $origin));
        }

        //Checks if the origin is an image
        if (!in_array(extension($origin), ['gif', 'jpg', 'jpeg', 'png'])) {
            throw new InternalErrorException(__d('thumbs', 'The file {0} is not an image', $origin));
        }
        
        //Sets the path, the width and the height of the origin file
        $this->origin = $origin;
        $this->width = getimagesize($origin)[0];
        $this->height = getimagesize($origin)[1];

        return $this;
    }

    /**
     * Downloads a file as a temporary file.
     * This is useful if the origin file is remote.
     * @param string $origin Remote origini
     * @return string Temporary file path
     * @throws NotFoundException
     */
    protected function _downloadTemporary($origin)
    {
        $tmp = sprintf('%s.%s', sys_get_temp_dir() . DS . md5($origin), extension($origin));
        
        //Downloads, if the file doesn't exist
        if (!file_exists($tmp)) {
            $fopen = fopen($origin, 'r');

            //Checks if it's readable
            if (!$fopen) {
                throw new NotFoundException(__d('thumbs', 'File or directory {0} not readable', $origin));
            }

            file_put_contents($tmp, $fopen);
        }
        
        //Marks as temporary file
        $this->temporary = true;

        return $tmp;
    }

    /**
     * Sets and gets the Imagick instance
     * @param string $origin Path of the origin file
     * @return \Imagick
     */
    protected function _getImagickInstance($origin)
    {
        //Creates the Imagick instance
        $imagick = new \Imagick($origin);

        //Strips all profiles and comments
        $imagick->stripImage();

        //For jpeg images, sets the image compression
        if (mime_content_type($origin) === 'image/jpeg') {
            $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
            $imagick->setImageCompressionQuality(100);
        }

        return $imagick;
    }

    /**
     * Creates a thumbnail
     * @param int $width Width
     * @param int $height Height
     * @param bool $force If `TRUE`, it forces the thumbnail to the desired sizes
     * @return string Thumbnail path
     * @throws InternalErrorException
     * @uses _downloadTemporary()
     * @uses _getImagickInstance()
     * @uses $height
     * @uses $origin
     * @uses $temporary
     * @uses $width
     */
    public function resize($width = 0, $height = 0, $force = false)
    {
        //It uses the shorter side if:
        // - the required size is not set;
        // - the required size exceeds the original size and it was not
        //  required to force the thumbnail sizes
        if ((empty($width) && empty($height)) || (!$force && ($width >= $this->width || $height >= $this->height))) {
            $width = $this->width;
            $height = $this->height;
        }
        
        //Sets the target path
        $target = THUMBS . DS . sprintf('resize_%s_w%s_h%s.%s', md5($this->origin), $width, $height, extension($this->origin));

        //If the thumbnail already exists, returns
        if (is_readable($target)) {
            return $target;
        }

        //If the required size exceed the original size and it was not required
        //to force the thumbnail sizes, it returns
        if (!$force && (($width && $width >= $this->width) || ($height && $height >= $this->height))) {
            //If it's a temporary file, copies as target
            if ($this->temporary) {
                (new File($this->origin))->copy($target);
                
                return $target;
            }

            return $this->origin;
        }

        //Writes the thumbnail
        $imagick = $this->_getImagickInstance($this->origin);
        $imagick->thumbnailImage($width, $height, $width && $height);
        $imagick->writeImage($target);
        $imagick->clear();

        return $target;
    }

    /**
     * Creates a square thumbnail
     * @param int $side Side
     * @param bool $force If `TRUE`, it forces the thumbnail to the desired sizes
     * @return string Thumbnail path
     * @throws InternalErrorException
     * @uses _downloadTemporary()
     * @uses _getImagickInstance()
     * @uses $height
     * @uses $origin
     * @uses $width
     */
    public function square($side = 0, $force = false)
    {
        //It uses the shorter side if:
        // - the required size is not set;
        // - the required size exceeds the original size and it was not
        //  required to force the thumbnail sizes
        if (empty($side) || (!$force && ($side >= $this->width || $side >= $this->height))) {
            $side = $this->width > $this->height ? $this->height : $this->width;
        }

        //Sets the target path
        $target = THUMBS . DS . sprintf('square_%s_s%s.%s', md5($this->origin), $side, extension($this->origin));

        //If the thumbnail already exists, returns
        if (is_readable($target)) {
            return $target;
        }
        
        //Writes the thumbnail
        $imagick = $this->_getImagickInstance($this->origin);
        $imagick->cropThumbnailImage($side, $side);
        $imagick->writeImage($target);
        $imagick->clear();

        return $target;
    }
}
