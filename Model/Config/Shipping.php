<?php
/* Glory to Ukraine! Glory to the heros! */
namespace Codelegacy\Shippingconfig\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Config;
use Magento\Backend\Block\Template\Context;
use Psr\Log\LoggerInterface;

class Shipping extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $scopeConfig;
    protected $shippingmodelconfig;
    protected $logger;

    public function __construct(
        Context $context,
        Config $shippingmodelconfig,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        $data = []
    ) {
        $this->shippingmodelconfig = $shippingmodelconfig;
        $this->scopeConfig         = $scopeConfig;
        $this->logger              = $logger;
        parent::__construct(
            $context,
            $data
        );
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $value = $element->getData('value');

        $options = $this->getActiveShippingMethod();
        $name = "groups[general][fields][shipping_method][value]";
        $id = "golden-select-shipping";

        $html = $this->getLayout()->createBlock(
            \Magento\Framework\View\Element\Html\Select::class
        )->setName(
            $name
        )->setId(
            $id
        )->setTitle(
            __("")
        )->setValue(
            $value
        )->setOptions(
            $options
        )->setExtraParams(
            'data-validate="{\'validate-select\':true}"'
        )->getHtml();

        return $html;
    }

    public function getActiveShippingMethod()
    {
        $shippings = $this->shippingmodelconfig->getActiveCarriers();
        $methods = [];

        foreach ($shippings as $shippingCode => $shippingModel) {
            if ($carrierMethods = $shippingModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $shippingCode.'_'.$methodCode;
                    $carrierTitle = $this->scopeConfig->getValue('carriers/'. $shippingCode.'/title');
                    $methods[] = ['value'=>$code,'label'=>$carrierTitle];
                }
            }
        }
        return $methods;
    }
}