<?php
/**
 * PHP / Markdown Extended : DocBook
 * @author      Pierre Cassat & contributors
 * @package     DocBook
 * @copyleft    Les Ateliers Pierrot <ateliers-pierrot.fr>
 * @license     GPL-v3
 * @sources     http://github.com/atelierspierrot/docbook
 */

namespace DocBook\WebFilesystem\DocBookFile;

use \DocBook\FrontController;
use \WebFilesystem\WebFileInfo;
use \DocBook\WebFilesystem\DocBookFileInterface;

class DBDirectory
    extends WebFileInfo
    implements DocBookFileInterface
{

    /**
     * @param array $params
     * @return string
     */
    public function viewFileInfos(array $params = array())
    {
        return FrontController::getInstance()
            ->display('', 'default_content', $params);
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