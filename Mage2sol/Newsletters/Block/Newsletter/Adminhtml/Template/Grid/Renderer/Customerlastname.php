<?php
namespace Mage2sol\Newsletters\Block\Newsletter\Adminhtml\Template\Grid\Renderer;
use Magento\Framework\DataObject;
class Customerlastname extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        if($row->getData('type')==1){
            return ($row->getData('c_lastname')!=''?$row->getData('c_lastname'):'----');
        }else{
            return ($row->getData('lastname')!=''?$row->getData('lastname'):'----');
        }
    }
}
