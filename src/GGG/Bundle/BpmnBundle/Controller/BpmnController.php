<?php

/*
 * This file is part of the GGG package.
 *
 * (c) Peter Tilsen
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GGG\Bundle\BpmnBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BpmnController extends ResourceController
{
    /**
     * @param Request $request
     * @param integer $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function odataAction(Request $request)
    {
        $bpmn = $this->get('sylius.repository.adjustment')->findAll();

        if (!$bpmn) {
            throw new NotFoundHttpException('Requested bpmn does not exist.');
        }

        return $this->render('SyliusWebBundle:Backend/Order:indexByUser.html.twig', array(
            'user'   => null,
            'orders' => null
        ));
    }
}
