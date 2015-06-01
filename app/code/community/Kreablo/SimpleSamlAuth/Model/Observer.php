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

    private $_customer;

    private $_updated = false;

    private static $_customAttributes = null;

    public function initiateCustomerSession( Varien_Event_Observer $observer )
    {
        $session = $observer->getData('customer_session');
        $customer = $this->_loadCustomerFromSamlSession();
        if ( $customer !== null ) {
            $session->setCustomer( $customer );
        }
    }

    private static function helper()
    {
        return Mage::helper('kreablo_simplesamlauth');
    }

    private function _loadCustomerFromSamlSession()
    {
        if ( ! self::helper()->isEnabled() ) {
            return null;
        }

        if ( isset( $this->_customer ) ) {
            return $this->_customer;
        }

        if ( self::helper()->getEmailAttribute() == '' ) {
            throw new Mage_Exception('Simple SAML Authentication is enabled but email attribute is not set.');
        }

        require_once self::helper()->getInstallationPath() . '/lib/_autoload.php';

        $ret = null;

        try {
            $as = new SimpleSAML_Auth_Simple( self::helper()->getAuthenticationSource() );

            Mage::log('initiateCustomerSession is authenticated: ' . $as->isAuthenticated() );

            if ( $as->isAuthenticated() ) {
                $attr = $as->getAttributes();

                if ( $this->_loadByEmail( $attr ) ) {
                    $this->loadCustomerAttributes( $attr );
                    $ret = $this->_customer;
                }
            }

        } catch (Exception $e) {
            Mage::logException( $e );
        }

        return $ret;
    }

    private function _loadByEmail( $attr )
    {
        $emailAttr = self::helper()->getEmailAttribute();
        if ( ! isset( $attr[ $emailAttr ] ) ) {
            Mage::log( "SAML Authenticated user do not have any email attribute (no value for $emailAttr).", Zend_Log::Err);
            return false;
        }

        $email = $attr[ $emailAttr ];

        $this->_customer = Mage::model('customer/customer')->loadByEmail( $email );

        if ( $this->_customer->getEmail() === null ) {
            $this->_customer->setEmail( $email );
            $this->_customer->setData('kreablo_simplesamlauth_created', 1);
        }

        return true;
    }

    private function _loadCustomerAttributes( $attr )
    {
        $this->_loadName( $attr );
        $this->_loadAddress( $attr );
        $this->_loadAttributes( $attr );
        $this->_loadCustomAttributes( $attr );
    }

    private function _eq( $v1, $v2 ) {
        if ( $v1 != $v2 ) {
            $this->_updated = true;
            return false;
        }
        return true;
    }

    private function _loadName( $attr )
    {
        $fnAttr = self::helper()->getGivenNameAttribute();
        if ( isset( $attr[ $fnAttr ] ) ) {
            $fn = $attr[ $fnAttr ];
            if ( $this->_eq( $self->_customer->getFirstname(), $fn ) ) {
                $self->_customer->setFirstname( $fn );
            }
        }

        $lnAttr = self::helper()->getSurnameAttribute();
        if ( isset( $attr[ $lnAttr ] ) ) {
            $ln = $attr[ $lnAttr ];
            if ( $this->_eq( $self->_customer->getLastname(), $ln ) ) {
                $self->_customer->setLastname( $ln );
            }
        }
    }

    private function _loadAddress( $attr ) {
    }

    private function _loadAttributes( $attr ) {
    }

    private static function _customAttributes() {
        if (self::$_customAttributes === null) {
            $custom = self::helper()->getCustomAttributes();

            $keyValues = preg_split('/(?<!\\);');

            $attributes = array();

            foreach ( $keyValues as $kv ) {
                if (preg_match( '/^((?:[^=]|\\=)*)=(.*)$/', $kv, $m ) ) {
                    $k = trim( stripslashes( $m[1] ) );
                    $v = trim( stripslashes( $m[2] ) );
                    $attributes[$k] = $v;
                }
            }

            self::$_customAttributes = $attributes;
        }

        return self::$_customAttributes;
    }

    private function _loadCustomAttributes( $attr ) {

        foreach ( self::_customAttributes() as $sourceAttribute => $targetAttribute ) {
            $this->_loadCustomAttribute( $attr, $sourceAttribute, $targetAttribute );
        }

    }

    private function _loadCustomAttribute( $attr, $sourceAttribute, $targetAttribute ) {
        if ( isset($attr[ $sourceAttribute ]) ) {
            $attrValue = $attr[ $sourceAttribute ];
            if ( ! $this->_eq( $this->_customer->getData( $targetAttribute ), $attrValue ) ) {
                $this->_customer->setData( $targetAttribute, $attrValue );
            }
        }
    }
}

