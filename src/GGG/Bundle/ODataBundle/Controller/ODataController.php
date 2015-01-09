<?php

/*
 * This file is part of the GGG package.
 *
 * (c) Peter Tilsen
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GGG\Bundle\ODataBundle\Controller;

use GGG\Bundle\ODataBundle\Repository\Entities;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ODataController extends Controller
{
    /**
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexAction(Request $request)
    {
        $odata = $this->get('ggg.odata.entities.repository');
        $entities = $odata->getEntities();
        return new JsonResponse(array(
                'content' => $entities
            ));
    }
}
