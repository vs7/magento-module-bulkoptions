<?php

class VS7_Bulkoptions_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tab_Bulkoptions extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('vs7_bulkoptions/attribute/options.phtml');
    }

    public function getColMappingsJson()
    {
        $columns = array();
        $colMappings = array();
        $colMappings[] = array('data: "id"');
        foreach ($this->getStores() as $_store) {
            $validator = '';
            if ($_store->getStoreId() == 0) {
                $validator = 'validator: store0_validator_fn, allowInvalid: false';
            }
            $colMappings[] = array('data: "store' . $_store->getStoreId() . '"', $validator);
        }
        foreach ($this->getStores() as $_store) {
            if ($_store->getStoreId() == 0) continue;
            $colMappings[] = array('data: "count' . $_store->getStoreId() . '"', '');
        }
        $colMappings[] = array('data: "sort_order"');
        $colMappings[] = array('data: "checked"', 'type: "checkbox"', 'checkedTemplate: "1"', 'uncheckedTemplate: ""');
        $colMappings[] = array('data: "delete"', 'type: "checkbox"');
        foreach ($colMappings as $row) {
            $columns[] = '{' . implode(',', $row) . '}';
        }
        return '[' . implode(',', $columns) . ']';
    }

    public function getColHeadersJson()
    {
        $colHeaders = array();
        $colHeaders[] = Mage::helper('catalog')->__('Option ID');
        foreach ($this->getStores() as $_store) {
            $colHeaders[] = $_store->getName();
        }
        foreach ($this->getStores() as $_store) {
            if ($_store->getStoreId() == 0) continue;
            $colHeaders[] = $_store->getName() . ' count';
        }
        $colHeaders[] = Mage::helper('catalog')->__('Position');
        $colHeaders[] = Mage::helper('catalog')->__('Is Default');
        $colHeaders[] = Mage::helper('catalog')->__('Delete');
        return Mage::helper('core')->jsonEncode($colHeaders);
    }

    public function getRowHeadersJson()
    {
        $rowHeaders = array();
        foreach ($this->getOptionValues() as $_value) {
            $rowHeaders[] = $_value->getId();
        }
        return Mage::helper('core')->jsonEncode($rowHeaders);
    }

    public function getDataJson()
    {
        $data = array();
        $attribute = $this->getAttributeObject();
        $count = array();
        foreach ($this->getStores() as $_store) {
            if ($_store->getStoreId() == 0) continue;
            $count[$_store->getStoreId()] = Mage::helper('vs7_bulkoptions')->getCount($attribute, $_store->getStoreId());
        }
        foreach ($this->getOptionValues() as $_value) {
            $value = array();
            foreach ($this->getStores() as $_store) {
                $value['store' . $_store->getStoreId()] = $_value->getData('store' . $_store->getStoreId());
            }
            foreach ($this->getStores() as $_store) {
                if ($_store->getStoreId() == 0) continue;
                $value['count' . $_store->getStoreId()] = isset($count[$_store->getStoreId()][$_value->getId()]) ? $count[$_store->getStoreId()][$_value->getId()] : 0;
            }
            $value['id'] = $_value->getId();
            $value['delete'] = false;
            $value['checked'] = ($_value->getChecked()) ? "1" : "";
            $value['intype'] = $_value->getIntype();
            $value['sort_order'] = $_value->getSortOrder();
            $data[] = $value;
        }
        return Mage::helper('core')->jsonEncode($data);
    }

    public function getDataByIdJson()
    {
        $data = array();
        $attribute = $this->getAttributeObject();
        $count = array();
        foreach ($this->getStores() as $_store) {
            if ($_store->getStoreId() == 0) continue;
            $count[$_store->getStoreId()] = Mage::helper('vs7_bulkoptions')->getCount($attribute, $_store->getStoreId());
        }
        foreach ($this->getOptionValues() as $_value) {
            $value = array();
            foreach ($this->getStores() as $_store) {
                $value['store' . $_store->getStoreId()] = $_value->getData('store' . $_store->getStoreId());
            }
            foreach ($this->getStores() as $_store) {
                if ($_store->getStoreId() == 0) continue;
                $value['count' . $_store->getStoreId()] = isset($count[$_store->getStoreId()][$_value->getId()]) ? $count[$_store->getStoreId()][$_value->getId()] : 0;
            }
            $value['checked'] = ($_value->getChecked()) ? "1" : "";
            $value['intype'] = $_value->getIntype();
            $value['sort_order'] = $_value->getSortOrder();
            $data[$_value->getId()] = $value;
        }
        return Mage::helper('core')->jsonEncode($data);
    }
}