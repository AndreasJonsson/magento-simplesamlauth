<?php

class Kreablo_SimpleSamlAuth_Block_Adminhtml_SimpleSamlAuth extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_simpleSamlAuth';
        $this->_headerText = Mage::helper('kreablo_simplesamlauth')->__('Configure SAML');

        parent::__construct();
        
        $this->_blockGroup = 'kreablo_simplesamlauth';

        $this->setChild('form', $childBlock);

        if (Mage::getSingleton('Admin/session')->isAllowed('simplesamlauth/configure')) {
            $this->_updateButton('save', 'label', Mage::helper('kreablo_simplesamlauth')->__('Save SimpleSamlPHP configuration'));
        } else {
            $this->_removeButton('save');
        }

    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->_blockGroup = 'kreablo_simplesamlauth';
        $childBlock = $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_Configure');
        $this->setChild('form', $childBlock);
    }

    public function getHeaderText()
    {
        return Mage::helper('kreablo_simplesamlauth')->__('Configure SimpleSamlPHP');
    }

}