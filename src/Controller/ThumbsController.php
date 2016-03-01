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
use Thumbs\Utility\ThumbCreator;

/**
 * Creates and displays thumbnails for image and video files
 */
class ThumbsController extends AppController {
	/**
	 * Internal function to render a thumbnail
	 * @param string $target Target file
	 */
	protected function _render($target) {
		if(is_url($target))
			return $this->redirect($target);
		
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
	 * @param string $origin Origin file path, encoded with `urlencode()` and `base64_encode()`
	 * @uses Thumbs\Utility\ThumbCreator::resize()
	 * @uses _render()
	 */
	public function resize($origin) {		
		$target = (new ThumbCreator(urldecode(base64_decode($origin))))
			->resize($this->request->query('width'), $this->request->query('height'));
		
		return $this->_render($target);
	}
	
	/**
	 * Resizes an images, creating a square thumbnail.
	 * 
	 * You have to set the maximum side as query string parameter (`side` parameter).
	 * @param string $origin Origin file path, encoded with `urlencode()` and `base64_encode()`
	 * @uses Thumbs\Utility\ThumbCreator::square()
	 * @uses _render()
	 */
	public function square($origin) {
		$target = (new ThumbCreator(urldecode(base64_decode($origin))))
			->square($this->request->query('side'));
		
		return $this->_render($target);
	}
}