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

class Kreablo_SimpleSamlAuth_Helper_Data extends Mage_Core_Helper_Data {

    /**
     * configuration key for simplesaml enabled flag.
     * 
     * @var boolean
     */
    const XML_PATH_ENABLED = 'simplesamlauth/simplesamlphp/enabled';

    /**
     * configuration key for simplesamlphp installation path.
     *
     * @var string
     */
    const XML_PATH_INSTALLATION_PATH = 'simplesamlauth/simplesamlphp/installation_path';

    /**
     * configuration key for the service provider
     *
     * @var string
     */
    const XML_PATH_SP = 'simplesamlauth/simplesamlphp/sp';

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
    const XML_PATH_EMAIL_ATTRIBUTE = 'simplesamlauth/simplesamlphp/email_attribute';

    /**
     * configuration key for the custom attribute mappings.
     *
     * @var string
     */
    const XML_PATH_CUSTOM_ATTRIBUTES = 'simplesamlauth/attribute_mapping/custom_attributes';

    /**
     * @return boolean Whether SAML authentication is enabled.
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    /**
     * @return string The configured installation path of simplesamlphp.
     */
    public function getInstallationPath()
    {
        return Mage::getStoreConfig(self::XML_PATH_INSTALLATION_PATH);
    }

    /**
     * @return string The configured service provider.
     */
    public function getAuthenticationSource()
    {
        return Mage::getStoreConfig(self::XML_PATH_SP);
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
        return Mage::getStoreConfig(self::XML_PATH_EMAIL_ATTRIBUTE);
    }

    /**
     * @return string a representation of the custom attributes.  The
     *                expected form is a semi-colon separated list of
     *                pairs on the form
     *                <source attribute>=<target attribute>
     *                example:
     *                school=customer_school_id;telephone=customer_telephone
     *
     *                Backslash may be used as an escape character.
     */
    public function getCustomAttributes()
    {
        return Mage::getStoreConfig(self::XML_PATH_CUSTOM_ATTRIBUTES);
    }
    
}