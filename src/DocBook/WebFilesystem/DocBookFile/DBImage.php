<?php
/**
 * This file is part of the DocBook package.
 *
 * Copyleft (ↄ) 2008-2015 Pierre Cassat <me@e-piwi.fr> and contributors
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
 * <http://github.com/atelierspierrot/docbook>.
 */

namespace DocBook\WebFilesystem\DocBookFile;

use \DocBook\FrontController;
use \WebFilesystem\WebFileInfo;
use \DocBook\WebFilesystem\DocBookFileInterface;
use \WebFilesystem\FileType\WebImage;

/**
 * Class DBImage
 */
class DBImage
    extends WebImage
    implements DocBookFileInterface
{

    /**
     * @param array $params
     * @return string
     */
    public function viewFileInfos(array $params = array())
    {
        $img = new WebImage($this->getRealPath());
        $params = array_merge($params, array(
            'height'=>$this->getHeight(),
            'width'=>$this->getWidth(),
        ));
        return FrontController::getInstance()
            ->display( $this->getBase64Content(true), 'embed_content', $params);
    }

    /**
     * @param array $params
     * @return string
     */
    public function getIntroduction(array $params = array())
    {
        return '';
    }

}

// Endfile
