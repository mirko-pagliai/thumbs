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
	 * You have to set the maximum width and/or the maximum height as options (`width` and `height` options).
	 * @param string $path Path to the image file
	 * @param array $options Array of HTML attributes
	 * @return string HTML code
	 */
	public function resize($path , array $options = []) {
		$height = empty($options['height']) ? NULL : $options['height'];
		$width = empty($options['width']) ? NULL : $options['width'];
		
		if($height || $width)
			$path = Router::url(['_name' => 'resize', base64_encode($path), '?' => compact('height', 'width')], TRUE);
		
		unset($options['height'], $options['width']);
						
		return $this->Html->image($path, $options);
	}
}