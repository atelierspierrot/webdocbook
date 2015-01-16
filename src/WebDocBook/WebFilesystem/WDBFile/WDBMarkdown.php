<?php
/**
 * This file is part of the WebDocBook package.
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
 * <http://github.com/wdbo/webdocbook>.
 */

namespace WebDocBook\WebFilesystem\WDBFile;

use \WebDocBook\FrontController;
use \WebFilesystem\WebFileInfo;
use \WebDocBook\WebFilesystem\WDBFileInterface;

/**
 * Class WDBMarkdown
 */
class WDBMarkdown
    extends WebFileInfo
    implements WDBFileInterface
{

    /**
     * @param array $params
     * @return string
     */
    public function viewFileInfos(array $params = array())
    {
        $wdb        = FrontController::getInstance();
        $md_parser  = $wdb->getMarkdownParser();
        $md_content = $md_parser->transformSource($this->getRealPath());
        $output_bag = $md_parser->get('OutputFormatBag');
        $page_notes = $md_content->getNotesToString();

        $params['page_notes'] = $page_notes;

        $page_footnotes = $md_content->getFootnotes();
        $page_glossary  = $md_content->getGlossaries();
        $page_citations = $md_content->getCitations();
        if (!empty($page_citations) || !empty($page_glossary)) {
            $params['page_footnotes']   = $page_footnotes;
            $params['page_glossary']    = $page_glossary;
            $params['page_citations']   = $page_citations;
        }

        $params['toc'] = $output_bag->getHelper()
            ->getToc($md_content, $output_bag->getFormatter());

        return $wdb->display($md_content->getBody(), 'content', $params);
    }

    /**
     * @param array $params
     * @return string
     */
    public function getIntroduction(array $params = array())
    {
        $wdb        = FrontController::getInstance();
        $md_parser  = $wdb->getMarkdownParser();
        $md_content = $md_parser->transformSource($this->getRealPath());
        return $md_content->getBody();
    }

}

// Endfile
