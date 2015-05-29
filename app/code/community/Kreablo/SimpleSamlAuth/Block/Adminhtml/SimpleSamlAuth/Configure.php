<?php

class Kreablo_SimpleSamlAuth_Block_Adminhtml_SimpleSamlAuth_Configure extends  Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'configure_form',
            'action' => $this->getUrl('*/configure'),
            'method' => 'post'
        ));

        $h = Mage::helper('kreablo_simplesamlauth');

        $fieldSet = $form->addFieldset('configure_fieldset', array(
            'legend' => $h->__('Configure'),
            'class' => 'fieldset-wide' ));

        $fieldSet->addField('configure_url', 'text', array(
            'name' => 'url',
            'label' => $h->__('URL of simplesamlphp discovery service'),
            'title' => $h->__('URL of simplesamlphp discovery service') ));

        $fieldSet->addField('configure_install_path', 'text', array(
            'name' => 'install-path',
            'label' => $h->__('Directory where simplesamlphp is installed'),
            'title' => $h->__('Directory where simplesamlphp is installed') ));

        $fieldSet->addField('configure_username_attribute', 'text', array(
            'name' => 'username-attribute',
            'label' => $h->__('Attribute to map to username field') ));

        $fieldSet->addField('configure_email_attribute', 'text', array(
            'name' => 'email-attribute',
            'label' => $h->__('Attribute to map to email field') ));

        $fieldSet->addField('configure_given_name_attribute', 'text', array(
            'name' => 'given-name-attribute',
            'label' => $h->__('Attribute to map to given name field') ));

        $fieldSet->addField('configure_surname_attribute', 'text', array(
            'name' => 'surname-attribute',
            'label' => $h->__('Attribute to map to surname field') ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }


}