<?php
/**
 * This file is part of the WebDocBook package.
 *
 * Copyleft (ↄ) 2008-2017 Pierre Cassat <me@picas.fr> and contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * The source code of this package is available online at 
 * <http://github.com/wdbo/webdocbook>.
 */

/**
 * Application mode: 'dev' or anything else (as prod)
 */
define('WEBDOCBOOK_MODE', 'prod');

/**
 * Show errors at least initially in dev mode
 *
 * `E_ALL` (-1) => for hard dev
 * `E_ALL & ~E_STRICT` => for hard dev in PHP5.4 avoiding strict warnings
 * `E_ALL & ~E_NOTICE & ~E_STRICT` => classic setting
 */
//@ini_set('display_errors',1); @error_reporting(-1);
//@ini_set('display_errors',1); @error_reporting(E_ALL & ~E_STRICT);
@ini_set('display_errors',1); @error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

// Get Composer autoloader
$composerAutoLoader = __DIR__.'/../src/vendor/autoload.php';
if (@file_exists($composerAutoLoader)) {
    require_once $composerAutoLoader;
} else {
    die("You need to run Composer on the project to build dependencies and auto-loading"
    ." (see: <a href=\"http://getcomposer.org/doc/00-intro.md#using-composer\">http://getcomposer.org/doc/00-intro.md#using-composer</a>)!");
}

// silent errors in production mode
if (!defined('WEBDOCBOOK_MODE') || WEBDOCBOOK_MODE!=='dev') {
    @ini_set('display_errors',0);
}

// the application
\WebDocBook\FrontController::getInstance()
    ->distribute();

// Endfile
