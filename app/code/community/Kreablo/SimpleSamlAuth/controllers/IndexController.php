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

class Kreablo_SimpleSamlAuth_IndexController extends Mage_Core_Controller_Front_Action
{

    private static function helper()
    {
        return Mage::helper('kreablo_simplesamlauth');
    }

    public function preDispatch()
    {
        if (! self::helper()->isEnabled()) {
            $this->_redirect('noRoute');
            return;
        }
    }

    public function indexAction()
    {
        $as = Mage::getSingleton('Kreablo_SimpleSamlAuth_Model_AuthenticationSource');

        $as->requireAuth();

        $session = Mage::getSingleton('customer/session');

        $nouserassoc = false;
        $error = false;

        if ( $this->getRequest()->isPost() ) {
            try {
                $nouserassoc = $this->getRequest()->getPost('nouserassoc', false);
                $userassoc = $this->getRequest()->getPost('userassoc');

                if ($nouserassoc) {
                    $errMsg = $as->createUserAssoc();
                } else {
                    $username = $userassoc['username'];
                    $password = $userassoc['password'];

                    if (empty($username) || empty($password)) {
                        $errMsg = $this->__('Login and password are required.');
                    } else {
                        $errMsg = $as->createUserAssoc($username, $password);
                    }
                }
            } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($userassoc['username']);
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($userassoc['username']);
                    $error = true;
            } catch (Exception $e) {
                $session->addError($this->__('Caught exception'));
                $error = true;
            }

            if (!empty($errMsg)) {
                $session->addError($errMsg);
                $error = true;
            }

        }

        if ( $error ) {
            $this->_redirect('*/*/');
        } elseif (!$nouserassoc && $as->needsUserAssoc()) {
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->renderLayout();
        } else {
            $this->_redirect('/customer/account');
        }
    }

}