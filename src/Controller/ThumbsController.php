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
namespace Thumbs\Controller;

use App\Controller\AppController;
use Cake\Network\Exception\NotFoundException;
use Thumbs\Utility\ThumbCreator;

/**
 * Creates and displays thumbnails for image and video files
 */
class ThumbsController extends AppController {
	/**
	 * Internal function to render a thumbnail
	 * @param string $target Target file to render
	 */
	protected function _render($target) {
		$this->autoRender = FALSE;

		//Renders the thumbnail
		header(sprintf('Content-type: %s', mime_content_type($target)));
		readfile($target);

		exit;
	}

	/**
	 * Resizes an images, creating a thumbnail.
	 * 
	 * You have to set the maximum width and/or the maximum height as query string parameters (`width` and `height` parameters).
	 * @param string $origin Origin file path, encoded with `base64_encode()`
	 * @throws NotFoundException
	 * @uses Thumbs\Utility\ThumbCreator::resize()
	 * @uses Thumbs\Utility\ThumbCreator::target()
	 * @uses _render()
	 */
	public function resize($origin) {
		$height = $this->request->query('height') ? $this->request->query('height') : 0;
		$width = $this->request->query('width') ? $this->request->query('width') : 0;
		
		//Checks for final size
		if(empty($height) && empty($width))
			throw new NotFoundException(__d('thumb', 'The final size are missing'));
		
		//Sets origin and target
		$origin = base64_decode($origin);
		$target = THUMBS.DS.sprintf('resize_%s_w%s_h%s.%s', md5($origin), $width, $height, extension($origin));
		
		//Creates the thumbnail, if doesn't exist
		if(!is_readable($target))
			(new ThumbCreator($origin))->target($target)->resize($width, $height);		
		
		$this->_render($target);
	}
}