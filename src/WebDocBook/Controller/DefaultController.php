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

namespace WebDocBook\Controller;

use \WebDocBook\Helper;
use \WebDocBook\Templating\Helper as TemplateHelper;
use \WebDocBook\Abstracts\AbstractController;
use \WebDocBook\Exception\NotFoundException;
use \WebDocBook\Model\File as WDBFile;

/**
 * Class DefaultController
 *
 * This is the default controller of WebDocBook, which may
 * handle most of the requests.
 */
class DefaultController
    extends AbstractController
{

    /**
     * Default action
     *
     * @param string $path
     * @return array
     */
    public function indexAction($path)
    {
        if (@is_dir($path)) {
            return $this->directoryAction($path);
        } else {
            return $this->fileAction($path);
        }
    }

    /**
     * Directory path action
     *
     * @param string $path
     * @return array
     * @throws \WebDocBook\Exception\NotFoundException
     */
    public function directoryAction($path)
    {
        try {
            $this->setPath($path);
        } catch (NotFoundException $e) {
            throw $e;
        }

        $dbfile = new WDBFile($this->getpath());
        if ($dbfile->isFile()) {
            return $this->fileAction($this->getPath());
        }

        $readme_content = $dir_content = '';

        $index = $dbfile->findIndex();
        if (file_exists($index)) {
            return $this->fileAction($index);
        }

        $tpl_params = array(
            'page'          => $dbfile->getWDBFullStack(),
            'dirscan'       => $dbfile->getWDBScanStack(),
            'breadcrumbs'   => TemplateHelper::getBreadcrumbs($this->getPath()),
            'title'         => TemplateHelper::buildPageTitle($this->getPath()),
        );
        if (empty($tpl_params['title'])) {
            if (!empty($tpl_params['breadcrumbs'])) {
                $tpl_params['title'] = TemplateHelper::buildPageTitle(end($tpl_params['breadcrumbs']));
            } else {
                $tpl_params['title'] = _T('Home');
            }
        }

        $readme = $dbfile->findReadme();
        if (file_exists($readme)) {
            $this->wdb->setInputFile($readme);
            $readme_dbfile  = new WDBFile($readme);
            $readme_content = $readme_dbfile->viewFileInfos();
        }
        $tpl_params['inpage_menu']  = !empty($readme_content) ? 'true' : 'false';

        $dir_content = $this->wdb
            ->display('', 'dirindex', $tpl_params);

        return array('default', $dir_content.$readme_content, $tpl_params);
    }

    /**
     * File path action
     *
     * @param string $path
     * @return array
     * @throws \WebDocBook\Exception\NotFoundException
     */
    public function fileAction($path)
    {
        try {
            $this->setPath($path);
        } catch (NotFoundException $e) {
            throw $e;
        }

        $dbfile = new WDBFile($this->getPath());
        if ($dbfile->isDir()) {
            return $this->directoryAction($this->getPath());
        }

        $tpl_params = array(
            'page'          => $dbfile->getWDBFullStack(),
            'breadcrumbs'   => TemplateHelper::getBreadcrumbs($this->getPath()),
            'title'         => TemplateHelper::buildPageTitle($this->getPath()),
        );
        if (empty($tpl_params['title'])) {
            if (!empty($tpl_params['breadcrumbs'])) {
                $tpl_params['title'] = TemplateHelper::buildPageTitle(end($tpl_params['breadcrumbs']));
            } else {
                $tpl_params['title'] = _T('Home');
            }
        }
        $content = $dbfile->viewFileInfos($tpl_params);

        return array('default',
            $content,
            $tpl_params);
    }

    /**
     * RSS action for concerned path
     *
     * @param string $path
     * @return array
     * @throws \WebDocBook\Exception\NotFoundException
     */
    public function rssFeedAction($path)
    {
        try {
            $this->setPath($path);
        } catch (NotFoundException $e) {
            throw $e;
        }

        $dbfile     = new WDBFile($this->getpath());
        $contents   = array();
        $tpl_params = array(
            'page'          => $dbfile->getWDBFullStack(),
            'breadcrumbs'   => TemplateHelper::getBreadcrumbs($this->getPath()),
            'title'         => TemplateHelper::buildPageTitle($this->getPath()),
        );
        if (empty($tpl_params['title'])) {
            if (!empty($tpl_params['breadcrumbs'])) {
                $tpl_params['title'] = TemplateHelper::buildPageTitle(end($tpl_params['breadcrumbs']));
            } else {
                $tpl_params['title'] = _T('Home');
            }
        }

        $this->wdb->getResponse()->setContentType('xml');

        $page = $dbfile->getWDBStack();
        if ($dbfile->isDir()) {
            $contents = Helper::getFlatDirscans($dbfile->getWDBScanStack(true), true);
            foreach ($contents['dirscan'] as $i=>$item) {
                if ($item['type']!=='dir' && file_exists($item['path'])) {
                    $dbfile = new WDBFile($item['path']);
                    $contents['dirscan'][$i]['content'] = $dbfile->viewIntroduction(4000, false);
                }
            }
        } else {
            $page['content'] = $dbfile->viewIntroduction(4000, false);
        }
        
        $rss_content = $this->wdb->display('', 'rss', array(
            'page'      => $page,
            'contents'  => $contents
        ));

        return array('layout_empty_xml', $rss_content);
    }

    /**
     * Sitemap action for a path
     *
     * @param string $path
     * @return array
     * @throws \WebDocBook\Exception\NotFoundException
     */
    public function sitemapAction($path)
    {
        try {
            $this->setPath($path);
        } catch (NotFoundException $e) {
            throw $e;
        }

        $dbfile = new WDBFile($this->getpath());
        if (!$dbfile->isDir()) {
            throw new NotFoundException(
                'Can not build a sitemap from a single file!',
                0, null, TemplateHelper::getRoute($dbfile->getWDBPath())
            );
        }

        $this->wdb->getResponse()->setContentType('xml');

        $contents       = Helper::getFlatDirscans($dbfile->getWDBScanStack(true));
        $rss_content    = $this->wdb->display('', 'sitemap', array(
            'page'          => $dbfile->getWDBStack(),
            'contents'      => $contents
        ));
        return array('layout_empty_xml', $rss_content);
    }

    /**
     * HTML only version of a path
     *
     * @param string $path
     * @return array
     * @throws \WebDocBook\Exception\NotFoundException
     */
    public function htmlOnlyAction($path)
    {
        try {
            $this->setPath($path);
        } catch (NotFoundException $e) {
            throw $e;
        }

        $dbfile = new WDBFile($this->getpath());
        if (!$dbfile->isFile()) {
            throw new NotFoundException(
                'Can not send raw content of a directory!',
                0, null, TemplateHelper::getRoute($dbfile->getWDBPath())
            );
        }

        $md_parser  = $this->wdb->getMarkdownParser();
        $md_content = $md_parser->transformSource($this->getPath());
        return array('layout_empty_html', 
            $md_content->getBody(),
            array('page_notes' => $md_content->getNotesToString())
        );
    }

    /**
     * Raw plain text action of a path
     *
     * @param string $path
     * @return array
     * @throws \WebDocBook\Exception\NotFoundException
     */
    public function plainTextAction($path)
    {
        try {
            $this->setPath($path);
        } catch (NotFoundException $e) {
            throw $e;
        }

        $dbfile = new WDBFile($this->getpath());
        if (!$dbfile->isFile()) {
            throw new NotFoundException(
                'Can not send raw content of a directory!',
                0, null, TemplateHelper::getRoute($dbfile->getWDBPath())
            );
        }

        $response = $this->wdb->getResponse();

        // mime header
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $this->getPath());
        if ( ! in_array($mime, $response::$content_types)) {
            $mime = 'text/plain';
        }
        finfo_close($finfo);

        // content
        $ctt = $response->flush(file_get_contents($this->getPath()), $mime);
        return array('layout_empty_txt', $ctt);
    }

    /**
     * Download action of a path
     *
     * @param string $path
     * @throws \WebDocBook\Exception\NotFoundException
     */
    public function downloadAction($path)
    {
        try {
            $this->setPath($path);
        } catch (NotFoundException $e) {
            throw $e;
        }

        $dbfile = new WDBFile($this->getpath());
        if (!$dbfile->isFile()) {
            throw new NotFoundException(
                'Can not send raw content of a directory!',
                0, null, TemplateHelper::getRoute($dbfile->getWDBPath())
            );
        }

        $this->wdb->getResponse()
            ->download($path, 'text/plain');
        exit(0);
    }

    /**
     * Global search action of a path
     *
     * @param string $path
     * @return array
     * @throws \WebDocBook\Exception\NotFoundException
     */
    public function searchAction($path)
    {
        try {
            $this->setPath($path);
        } catch (NotFoundException $e) {
            throw $e;
        }

        $search = $this->wdb->getRequest()->getArgument('s');
        if (empty($search)) {
            return $this->indexAction($path);
        }

        $_s = Helper::makeSearch($search, $this->getPath());

        $title          = _T('Search for "%search_str%"', array('search_str'=>$search));
        $breadcrumbs    = TemplateHelper::getBreadcrumbs($this->getPath());
        $breadcrumbs[]  = $title;
        $dbfile         = new WDBFile($this->getpath());
        $page           = $dbfile->getWDBStack();
        $page['type']   = 'search';
        $tpl_params     = array(
            'page'          => $page,
            'breadcrumbs'   => $breadcrumbs,
            'title'         => $title,
        );

        $search_content = $this->wdb->display($_s, 'search', array(
            'search_str'    => $search,
            'path'          => TemplateHelper::buildPageTitle($this->getPath()),
        ));
        return array('default', $search_content, $tpl_params);
    }

}

// Endfile
