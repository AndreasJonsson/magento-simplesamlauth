<?php
/**
 * SimpleSAMLauth installation script
 *
 * @author Andreas Jonsson, Kreablo AB
 */

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;

/**
 * Creating table userassoc
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('kreablo_simplesamlauth_userassoc/userAssoc'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Entity id')
    ->addColumn('idp_user_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'IDP user id')
    ->addColumn('local_user_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => false,
    ), 'Local user id')
    ->addIndex($installer->getIdxName(
            $installer->getTable('kreablo_simplesamlauth_userassoc/userAssoc'),
            array('idp_user_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('idp_user_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addForeignKey($installer->getFkName(
             $installer->getTable('kreablo_simplesamlauth_userassoc/userAssoc'),
             'local_user_id',
             $installer->getTable('customer/entity'),
             'email' 
         ),
        'local_user_id',
         $installer->getTable('customer/entity'),
         'email',
          Varien_Db_Ddl_Table::ACTION_CASCADE,
          Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('SAML user account associations');

$installer->getConnection()->createTable($table);
