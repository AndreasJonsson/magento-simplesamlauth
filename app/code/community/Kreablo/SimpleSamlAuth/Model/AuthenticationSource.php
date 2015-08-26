<?php

class Kreablo_SimpleSamlAuth_Model_AuthenticationSource
{

    private $_customer;

    private $_updated = false;

    private $_new = false;

    private $_as = null;

    private $_email_mapped = null;

    private static $_customAttributes = null;

    private static function helper()
    {
        return Mage::helper('kreablo_simplesamlauth');
    }

    private function _as()
    {
        if ($this->_as === null) {
            require_once self::helper()->getInstallationPath() . '/lib/_autoload.php';

            $this->_as = new SimpleSAML_Auth_Simple( self::helper()->getAuthenticationSource() );
        }

        return $this->_as;
    }

    /**
     * Perform authentication with simpleSAMLphp.
     *
     * @param $options Options to pass to simpleSAMLphp.
     */
    public function requireAuth( $options = array() )
    {
        $this->_as()->requireAuth( $options );
    }

    /**
     * @return true if a user association needs to be created for the
     * SSO user.  If user association needs is disabled in the
     * administrative settings, this will always return false.
     */
    public function needsUserAssoc()
    {
        $attr = $this->_as()->getAttributes();

        return $this->_idpEmailMapped( $attr ) === null;
    }

    public function createUserAssoc( $username = null, $password = null )
    {
        $attr = $this->_as()->getAttributes();

        $idpEmail = $this->_idpEmail( $attr );

        if (!$this->_loadByEmail( $attr, $username === null ? $idpEmail : $username )) {
            return self::helper()->__('Failed to initiate customer model!');
        }

        if ($username !== null) {
            if ($this->_customer->getConfirmation() && $this->_customer->isConfirmationRequired()) {
                return Mage::helper('customer')->__('This account is not confirmed.');
            }
            if (!$this->_customer->validatePassword($password)) {
                return Mage::helper('customer')->__('Invalid login or password.');
            }
        } else {
            if (!$this->_new) {
                $url = Mage::getUrl('customer/account/forgotpassword');
                $message = self::helper()->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                return $message;
            }

            $username = $idpEmail;
        }

        $this->_loadCustomerAttributes( $attr );
        if ($this->_updated) {
            $this->_customer->save();
        }

        $ua = Mage::getModel('kreablo_simplesamlauth/userAssoc');
        $ua->setIdpUserId( $idpEmail );
        $ua->setLocalUserId( $username );
        $ua->setOrigData();
        $ua->save();

        return null;
    }

    public function loadCustomerFromSamlSession()
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

        $ret = null;

        try {

            if ( $this->_as()->isAuthenticated() ) {
                $attr = $this->_as()->getAttributes();

                $session = Mage::getSingleton( 'core/session' );

                if ( $this->_loadByEmail( $attr ) ) {
                    $this->_loadCustomerAttributes( $attr );
                    if ($this->_updated) {
                        $this->_customer->save();
                    }
                    $ret = $this->_customer;
                }
            }

        } catch (Exception $e) {
            Mage::logException( $e );
        }

        return $ret;
    }

    private function _idpEmail( $attr )
    {
        $emailAttr = self::helper()->getEmailAttribute();
        if ( ! isset( $attr[ $emailAttr ] ) || count( $attr[ $emailAttr] ) === 0 ) {
            Mage::log( "SAML Authenticated user do not have any email attribute (no value for $emailAttr).", Zend_Log::ERR);
            return false;
        }

        $email = $attr[ $emailAttr ][0];

        if ( empty($email) ) {
            Mage::throwException('No email was set in the SAML attribute (' . $emailAttr . ')' );
        }

        return $email;
    }

    private function _idpEmailMapped( $attr )
    {
        if ($this->_email_mapped === null) {
            $email = $this->_idpEmail( $attr );

            if ( !self::helper()->isUserAssocEnabled() ) {
                Mage::log( 'Userassoc not enabled returning email: ' . $email );
                $this->_email_mapped = $email;
                return $email;
            }

            $ua = Mage::getModel('kreablo_simplesamlauth/userAssoc');
            $ua->load( $email, 'idp_user_id' );

            if ( $ua->isObjectNew() ) {
                return null;
            }

            $this->_email_mapped = $ua->getLocalUserId();
        }

        return $this->_email_mapped;
    }

    private function _loadByEmail( $attr, $email = null )
    {

        if ( $email === null ) {
            $email = $this->_idpEmailMapped( $attr );
        }

        if ( empty($email) ) {
            Mage::log('No email association was found for the email (' . $this->_idpEmail( $attr ) . ')' );
            return false;
        }

        Mage::log( 'Loading user with email ' . $email );

        $this->_customer = Mage::getModel('customer/customer');
        $this->_customer->setWebsiteId( Mage::app()->getStore()->getWebsiteId() );
        $this->_customer->loadByEmail( $email );

        if ( $this->_customer->getEmail() === null ) {
            $this->_customer->setEmail( $email );
            $this->_customer->setData('kreablo_simplesamlauth_created', 1);
            $this->_new = true;
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
        if ( isset( $attr[ $fnAttr ] ) && count( $attr[ $fnAttr ] ) > 0) {
            $fn = $attr[ $fnAttr ][0];
            if ( ! $this->_eq( $this->_customer->getFirstname(), $fn ) ) {
                $this->_customer->setFirstname( $fn );
            }
        }

        $lnAttr = self::helper()->getSurnameAttribute();
        if ( isset( $attr[ $lnAttr ] ) && count( $attr[ $lnAttr ] ) > 0) {
            $ln = $attr[ $lnAttr ][0];
            if ( ! $this->_eq( $this->_customer->getLastname(), $ln ) ) {
                $this->_customer->setLastname( $ln );
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

            $keyValues = preg_split('/(?<!\\\\);/', $custom);

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
        if ( isset($attr[ $sourceAttribute ]) && count($attr[ $sourceAttribute ]) > 0) {
            $attrValue = $attr[ $sourceAttribute ][0];
            if ( ! $this->_eq( $this->_customer->getData( $targetAttribute ), $attrValue ) ) {
                $this->_customer->setData( $targetAttribute, $attrValue );
            }
        }
    }

}