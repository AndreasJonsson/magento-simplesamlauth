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

/**
 * Wrapper for magento session store.  The installation path of
 * magento must be configured in config.php by setting the parameter
 * 'magento.store.magentodir'.
 *
 * Use this by setting store.type = 'magentostore:Mage' in config.php;
 */
class sspmod_magentostore_Store_Mage extends SimpleSAML_Store
{

    private $_mage_session = null;

    public function __construct()
    {
        $config = SimpleSAML_Configuration::getInstance();
        $magentoDir = $config->getString('magento.store.magentodir', null);
        if ($magentoDir == null) {
            throw new SimpleSAML_Error_Exception('Magento session store type requires a value for configration parameter magento.store.magentodir');
        }

        require_once( $magentoDir . '/app/Mage.php' );

        if (Mage::getRoot() == null) {
            Mage::init('', 'store');
        }
        $cookieName = $config->getString('session.phpsession.cookiename', 'simplesaml');
        $this->_mage_session = Mage::getModel('core/session', array( 'name' => $cookieName ) );
        SimpleSAML_Logger::debug('mage session id: ' . $this->_mage_session->getEncryptedSessionId() );
    }

    private static function k($type, $key)
    {
        return 'simpleSAMLphp.' . $type . '.' . $key;
    }

    /**
     * Retrieve a value from the datastore.
     *
     * @param string $type  The datatype.
     * @param string $key  The key.
     * @return mixed|NULL  The value.
     */
    public function get($type, $key)
    {
//        SimpleSAML_Logger::debug('Getting ' . self::k($type, $key) . ':  ' . print_r($this->_mage_session->getData( self::k($type, $key) ), true ) );
        return $this->_mage_session->getData( self::k($type, $key) );
    }


    /**
     * Save a value to the datastore.
     *
     * @param string $type  The datatype.
     * @param string $key  The key.
     * @param mixed $value  The value.
     * @param int|NULL $expire  The expiration time (unix timestamp), or NULL if it never expires.
     */
    public function set($type, $key, $value, $expire = NULL)
    {
        //      SimpleSAML_Logger::debug('Setting ' . self::k($type, $key) . ':  ' . print_r($value, true));
        $this->_mage_session->setData(self::k($type, $key), $value);
    }


    /**
     * Delete a value from the datastore.
     *
     * @param string $type  The datatype.
     * @param string $key  The key.
     */
    public function delete($type, $key)
    {
//        SimpleSAML_Logger::delete('Deleting ' . self::k($type, $key) );
        $this->_mage_session->unsetData(self::k($type, $key));
    }

}

