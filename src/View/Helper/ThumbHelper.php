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
namespace Thumbs\View\Helper;

use Cake\View\Helper;
use Cake\Routing\Router;

/**
 * Allows to create thumbnails
 */
class ThumbHelper extends Helper {
	/**
	 * Helpers
	 * @var array 
	 */
    public $helpers = ['Html'];
	
	/**
	 * Resizes an images, creating a thumbnail.
	 * 
	 * You have to set `height` and/or `width` option.
	 * @param string $path Path to the image file
	 * @param array $options Array of HTML attributes
	 * @return string HTML code
	 */
	public function resize($path , array $options = []) {
		$height = empty($options['height']) ? 0 : $options['height'];
		$width = empty($options['width']) ? 0 : $options['width'];
		unset($options['height'], $options['width']);
		
		if($height || $width)
			$path = Router::url(['_name' => 'resize', base64_encode($path), '?' => compact('height', 'width')], TRUE);
						
		return $this->Html->image($path, $options);
	}
	
	/**
	 * Resizes an images, creating a square thumbnail.
	 * 
	 * You have to set the `side` option.
	 * @param string $path Path to the image file
	 * @param array $options Array of HTML attributes
	 * @return string HTML code
	 */
	public function square($path, array $options = []) {
		$side = empty($options['side']) ? NULL : $options['side'];
		unset($options['side']);
		
		if($side)
			$path = Router::url(['_name' => 'square', base64_encode($path), '?' => compact('side')], TRUE);
						
		return $this->Html->image($path, $options);
	}
	
	/**
	 * Resizes an images, creating a thumbnail.
	 * 
	 * You have to:
	 *	* set the `side` option, if you want to create a square thumbnail;
	 *	* set the `height` and/or `width` option, if you want to create a simple thumbnail.
	 * @param string $path Path to the image file
	 * @param array $options Array of HTML attributes
	 * @return string HTML code
	 */
//	public function resize($path , array $options = []) {
//		//Checks for "side" option
//		if(!empty($options['side']))
//			$size['side'] = $options['side'];
//		//Else, checks for "height" and/or "width options
//		else {
//			if(!empty($options['height']))
//				$size['height'] = $options['height'];
//			if(!empty($options['width']))
//				$size['width'] = $options['width'];
//		}
//		
//		unset($options['height'], $options['side'], $options['width']);
//						
//		return $this->Html->image(empty($size) ? $path : Router::url(['_name' => 'resize', base64_encode($path), '?' => $size], TRUE), $options);
//	}
}