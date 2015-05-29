<?php
/**
 * Controller for administrating simplesamlphp.
 *
 * @author Andreas Jonsson 
 */
class Kreablo_SimpleSamlAuth_Adminhtml_SimpleSamlAuthController extends Mage_adminhtml_Controller_Action
{

	public function indexAction()
	{
		$this->loadLayout();
		$this->_title( $this->__('Simple SAML Authentication') )
			->_title( $this->__('Configure SimpleSamlPHP') );

		$this->renderLayout();
	}

	public function configureAction()
	{
		$redirectPath = '*/*';
		$data = $this->getRequest()->getPost();
		if ($data) {
			$hasError = false;

			

			if ($hasError) {
				$this->getSession->setFormData($data);
			}
		}
		$this->_redirect($redirectPath, array());
	}

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'configure':
                return Mage::getSingleton('admin/session')->isAllowed('simplesamlauth/configure');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('simplesamlauth');
                break;
        }
    }

}
