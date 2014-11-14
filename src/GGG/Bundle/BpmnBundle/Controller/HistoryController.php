<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * shortdescription
 *
 * description
 *
 * PHP version 5.3
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Name of subfolder [module|install|..]
 * @package    Name of component [sales|payment|..]
 * @author     Peter Lukasiewicz <peter@nu3.de>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @copyright  2014 nu3 GmbH
 * @since      Class aviable since 2012
 * @version    CVS: $Id: Category.php 3831 2013-01-22 11:20:04Z peter.lukasiewicz $
 */
namespace GGG\Bundle\BpmnBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\FOSRestController;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HistoryController extends FOSRestController
{
    /**
     *
     * @ApiDoc(
     *  resource=true,
     *  description="This is a description of your API method",
     *  filters={
     *      {"name"="a-filter", "dataType"="integer"},
     *      {"name"="another-filter", "dataType"="string", "pattern"="(foo|bar) ASC|DESC"}
     *  }
     * )
     *
     */
    public function allAction()
    {
        $history = array('history1', 'history2');
        $view = $this->view($history);
        $view->setFormat('json');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     *
     * @ApiDoc(
     *  description="Returns a collection of Object",
     *  requirements={
     *      {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="how many objects to return"
     *      }
     *  },
     *  parameters={
     *      {"name"="categoryId", "dataType"="integer", "required"=true, "description"="category id"}
     *  }
     * )
     *
     */
    public function getAction($id)
    {
        $history = array('history1');

        return array('history' => $history);
    }
}