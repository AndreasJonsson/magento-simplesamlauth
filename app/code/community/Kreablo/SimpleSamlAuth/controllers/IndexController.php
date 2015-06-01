<?php
/*
 * Copyright 2015  Andreas Jonsson
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Kreablo_SimpleSamlAuth_IndexController extends Mage_Core_Controller_Front_Action
{

    private static function helper()
    {
        return Mage::helper('kreablo_simplesamlauth');
    }

    public function preDispatch()
    {
        if (! self::helper()->isEnabled()) {
            $this->_redirect('noroute');
            return;
        }
    }

    public function indexAction()
    {
        require_once self::helper()->getInstallationPath() . '/lib/_autoload.php';

        $as = new SimpleSAML_Auth_Simple( self::helper()->getAuthenticationSource() );

        $as->requireAuth();

        $this->_redirect('/customer/account');
    }

}