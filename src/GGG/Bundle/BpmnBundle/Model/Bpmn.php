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
namespace GGG\Bundle\BpmnBundle\Model;

class Bpmn implements BpmnInterface
{
    /**
     * Address id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * First name.
     *
     * @var string
     */
    protected $firstName;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }
}