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
use Cake\Filesystem\Folder;
use Thumbs\Utility\ThumbCreator;

/**
 * Creates and displays thumbnails for image and video files
 */
class ThumbsController extends AppController {
	/**
	 * Thumbnail path
	 * @var string
	 */
	protected $thumb;
	
	/**
	 * Resizes an images, creating a thumbnail.
	 * 
	 * You have to set the maximum width and/or the maximum height as query string parameters (`width` and `height` parameters).
	 * @param string $file File path, encoded with `base64_encode()`
	 * @uses Thumbs\Utility\ThumbCreator::resize()
	 * @uses Thumbs\Utility\ThumbCreator::target()
	 * @uses $thumb
	 */
	public function resize($file) {
		$height = $this->request->query('height');
		$width = $this->request->query('width');
		$file = base64_decode($file);
		
		//If there are not the final size, the original file is the thumbnail
		if(empty($height) && empty($width)) {
			$this->thumb = $file;
			return;
		}
		
		//If the file is local and its path is relative, then the path will be relative to `webroot/img`
		$file = Folder::isAbsolute($file) ? $file : WWW_ROOT.'img'.DS.$file;
		
		//If the required size exceed the original size
		if(($width && $width >= getimagesize($file)[0]) || ($height && $height >= getimagesize($file)[1])) {
			$this->thumb = $file;
			return;
		}
		
		//If the origin file is a remote file, removes the query string
		$file = is_url($file) ? explode('?', $file, 2)[0] : $file;
		
		//Sets the thumbnail path
		$this->thumb = THUMBS.DS.sprintf('resize_%s', md5($file));
		$this->thumb .= $width ? sprintf('_w%s', $width) : NULL;
		$this->thumb .= $height ? sprintf('_h%s', $height) : NULL;
		$this->thumb = sprintf('%s.%s', $this->thumb, strtolower(pathinfo($file, PATHINFO_EXTENSION)));
		
		//Returns, if the thumbnail already exists
		if(file_exists($this->thumb))
			return;

		//Creates the thumbnail
		(new ThumbCreator($file))->target($this->thumb)->resize($width, $height);
	}
	
	/**
	 * Called after the controller action is run, but before the view is rendered.
	 * You can use this method to perform logic or set view variables that are required on every request.
	 * @param \Cake\Event\Event $event An Event instance
	 * @see http://api.cakephp.org/3.2/class-Cake.Controller.Controller.html#_beforeRender
	 * @uses $thumb
	 */
	public function beforeRender(\Cake\Event\Event $event) {
        $this->autoRender = FALSE;
		
		//Renders the thumbnail
		header(sprintf('Content-type: %s', mime_content_type($this->thumb)));
        readfile($this->thumb);
		
		exit;
	}
}