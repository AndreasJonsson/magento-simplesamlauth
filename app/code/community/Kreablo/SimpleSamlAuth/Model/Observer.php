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

class Kreablo_SimpleSamlAuth_Model_Observer
{

    public function initiateCustomerSession( Varien_Event_Observer $observer )
    {
        $session = $observer->getData('customer_session');
        $customer = Mage::getSingleton('Kreablo_SimpleSamlAuth_Model_AuthenticationSource')->loadCustomerFromSamlSession();
        if ( $customer !== null ) {
            $session->setCustomer( $customer );
        }
    }

}

