<?php

class VS7_Bulkoptions_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCount($attribute, $storeId = 0)
    {
//        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'skincare_sun_protection_filter');

        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $select = Mage::getResourceModel('catalog/product_collection')->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS);

        $tableAlias = $attribute->getAttributeCode() . '_idx';
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $storeId),
        );
        $select
            ->join(
                array($tableAlias => $resource->getTableName('catalog/product_index_eav')),
                join(' AND ', $conditions),
                array('value', 'count' => "COUNT(DISTINCT {$tableAlias}.entity_id)"))
            ->group("{$tableAlias}.value");

        $optionsCount = $connection->fetchPairs($select);

        return $optionsCount;
    }
}