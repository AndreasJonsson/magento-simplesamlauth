<?php

class Kreablo_SimpleSamlAuth_Model_Resource_UserAssoc extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('kreablo_simplesamlauth_userassoc/userAssoc', 'id');
    }
}
