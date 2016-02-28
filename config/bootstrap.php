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

//Sets the default directory
if(!defined('THUMBS'))
	define('THUMBS', TMP.'thumbs');

if(!function_exists('extension')) {
	/**
	 * Returns the file extension.
	 * 
	 * If it's an url, strips the query string.
	 * @param string $file File
	 * @return string Extension
	 */
	function extension($file) {
		return strtolower(pathinfo(explode('?', $file, 2)[0], PATHINFO_EXTENSION));
	}
}

if(!function_exists('is_url')) {
	/**
	 * Checks whether a url is invalid
	 * @param string $url Url
	 * @return bool
	 */
	function is_url($url) {
		return (bool) preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url);
	}
}