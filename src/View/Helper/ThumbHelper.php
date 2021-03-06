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
namespace Thumbs\View\Helper;

use Cake\Routing\Router;
use Cake\View\Helper;

/**
 * Allows to create thumbnails
 */
class ThumbHelper extends Helper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = ['Html'];

    /**
     * Resizes an images, creating a thumbnail.
     *
     * You have to set `height` and/or `width` option.
     * Optionally, you can set `force` option.
     * @param string $path Path to the image file
     * @param array $options Array of HTML attributes
     * @return string
     * @uses resizeUrl()
     */
    public function resize($path, array $options = [])
    {
        $path = $this->resizeUrl($path, $options);

        unset($options['force'], $options['height'], $options['width']);

        return $this->Html->image($path, $options);
    }

    /**
     * Gets the url for a thumbnail.
     *
     * You have to set `height` and/or `width` option.
     * Optionally, you can set `force` option.
     * @param string $path Path to the image file
     * @param array $options Array of HTML attributes
     * @return string
     */
    public function resizeUrl($path, array $options = [])
    {
        if (!empty($options['width'])) {
            $size['width'] = $options['width'];
        }
        if (!empty($options['height'])) {
            $size['height'] = $options['height'];
        }
        if (!empty($options['force'])) {
            $size['force'] = true;
        }

        if (!empty($size)) {
            $path = Router::url([
                '_name' => 'resize',
                encodePath($path),
                '?' => $size
            ], true);
        }

        return $path;
    }

    /**
     * Resizes an images, creating a square thumbnail.
     *
     * You have to set the `side` option.
     * Optionally, you can set `force` option.
     * @param string $path Path to the image file
     * @param array $options Array of HTML attributes
     * @return string
     * @uses squareUrl()
     */
    public function square($path, array $options = [])
    {
        $path = $this->squareUrl($path, $options);

        unset($options['force'], $options['side']);

        return $this->Html->image($path, $options);
    }

    /**
     * Gets the url for a square thumbnail.
     *
     * You have to set the `side` option.
     * Optionally, you can set `force` option.
     * @param string $path Path to the image file
     * @param array $options Array of HTML attributes
     * @return string
     */
    public function squareUrl($path, array $options = [])
    {
        if (!empty($options['side'])) {
            $size['side'] = $options['side'];
        }
        if (!empty($options['force'])) {
            $size['force'] = true;
        }

        if (!empty($size)) {
            $path = Router::url([
                '_name' => 'square',
                encodePath($path),
                '?' => $size
            ], true);
        }

        return $path;
    }

    /**
     * Convenient alias for `resize()` and `square()` methods.
     * It determines which method to use depending on the passed options.
     * @param string $path Path to the image file
     * @param array $options Array of HTML attributes
     * @return string
     * @uses resize()
     * @uses square()
     */
    public function image($path, array $options = [])
    {
        if (!empty($options['side'])) {
            return $this->square($path, $options);
        }

        return $this->resize($path, $options);
    }

    /**
     * Convenient alias for `resizeUrl()` and `squareUrl()` methods.
     * It determines which method to use depending on the passed options.
     * @param string $path Path to the image file
     * @param array $options Array of HTML attributes
     * @return string
     * @uses resizeUrl()
     * @uses squareUrl()
     */
    public function url($path, array $options = [])
    {
        if (!empty($options['side'])) {
            return $this->squareUrl($path, $options);
        }

        return $this->resizeUrl($path, $options);
    }
}
