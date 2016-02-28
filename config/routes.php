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

use Cake\Routing\Router;

Router::plugin('Thumbs', ['path' => '/'], function ($routes) {
	$routes->connect('/resize/*', ['controller' => 'Thumbs', 'action' => 'resize', 'plugin' => 'Thumbs'], ['_name' => 'resize']);
	$routes->connect('/square/*', ['controller' => 'Thumbs', 'action' => 'square', 'plugin' => 'Thumbs'], ['_name' => 'square']);
	
    $routes->fallbacks('DashedRoute');
});
