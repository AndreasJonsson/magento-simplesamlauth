<?php
class Kreablo_SimpleSamlAuth_Helper_Data extends Mage_Core_Helper_Data {

    /**
     * configuration key for simplesamlphp installation path.
     *
     * @var string
     */
    const XML_PATH_INSTALLATION_PATH = 'simplesamlauth/simplesamlphp/installation_path';

    /**
     * configuration key for the url of the simplesamlphp discovery service.
     *
     * @var string
     */
    const XML_PATH_URL = 'simplesamlauth/simplesamlphp/url';

    /**
     * configuration key for the username attribute to be used form mapping the user field.
     *
     * @var string
     */
    const XML_PATH_USERNAME_ATTRIBUTE = 'simplesamlauth/attribute_mapping/username_attribute';

    /**
     * configuration key for the given name attribute to be used form mapping the user field.
     *
     * @var string
     */
    const XML_PATH_GIVEN_NAME_ATTRIBUTE = 'simplesamlauth/attribute_mapping/given_name_attribute';

    /**
     * configuration key for the surname attribute to be used form mapping the user field.
     *
     * @var string
     */
    const XML_PATH_SURNAME_ATTRIBUTE = 'simplesamlauth/attribute_mapping/surname_attribute';

    /**
     * configuration key for the email attribute to be used form mapping the user field.
     *
     * @var string
     */
    const XML_PATH_EMAIL_ATTRIBUTE = 'simplesamlauth/attribute_mapping/email_attribute';

    /**
     * @return string The configured installation path of simplesamlphp.
     */
    public function getInstallationPath()
    {
        return Mage::getStoreConfig(self::XML_PATH_INSTALLATION_PATH);
    }

    /**
     * @return string The configure URL of the simplesamlphp discovery service.
     */
    public function getUrl()
    {
        return Mage::getStoreConfig(self::XML_PATH_URL);
    }

    /**
     * @return string The attribute to map to the username field.
     */
    public function getUsernameAttribute()
    {
        return Mage::getStoreConfig(self::XML_PATH_USERNAME_ATTRIBUTE);
    }

    /**
     * @return string The attribute to map to the given name field.
     */
    public function getGivenNameAttribute()
    {
        return Mage::getStoreConfig(self::XML_PATH_GIVEN_NAME_ATTRIBUTE);
    }

    /**
     * @return string The attribute to map to the user's surname.
     */
    public function getSurnameAttribute()
    {
        return Mage::getStoreConfig(self::XML_PATH_SURNAME);
    }

    /**
     * @return string The attribute to map to the user's email address.
     */
    public function getEmailAttribute()
    {
        return Mage::getStoreConfig(self::XML_PATH_EMAP_ATTRIBUTE);
    }
}