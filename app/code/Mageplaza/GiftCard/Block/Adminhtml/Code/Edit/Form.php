<?php


namespace Mageplaza\GiftCard\Block\Adminhtml\Code\Edit;


class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    protected $_systemStore;


    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Magento\Framework\Registry $registry,
                                \Magento\Store\Model\System\Store $systemStore,
                                \Magento\Framework\Data\FormFactory $formFactory,
                                array $data = [])
    {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
