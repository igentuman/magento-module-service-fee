<?php
/*
 * This module was made as test task during recruitment.
 *
 * MIT License
 *
 * Copyright (c) 2023 Siarhei Astapchyk <igentuman@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

declare(strict_types=1);

namespace Igentu\ServiceFee\Block\Adminhtml\Rule\Edit\Tab;

use Igentu\ServiceFee\Model\RuleFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as RuleConditions;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\SalesRule\Model\Rule;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Block for rendering Conditions tab on Rules creation page.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Conditions extends Generic implements
    TabInterface
{
    protected Fieldset $_rendererFieldset;

    protected RuleConditions $_conditions;

    protected $_nameInLayout = 'conditions_apply_to';

    private RuleFactory $ruleFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param RuleConditions $conditions
     * @param Fieldset $rendererFieldset
     * @param array $data
     * @param RuleFactory|null $ruleFactory
     */
    public function __construct(
        Context        $context,
        Registry       $registry,
        FormFactory    $formFactory,
        RuleConditions $conditions,
        Fieldset       $rendererFieldset,
        array          $data = [],
        RuleFactory    $ruleFactory = null
    )
    {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        $this->ruleFactory = $ruleFactory ?: ObjectManager::getInstance()
            ->get(RuleFactory::class);
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     *
     * @codeCoverageIgnore
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * @inheritdoc
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('servicefee_rule');
        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Handles addition of conditions tab to supplied form.
     *
     * @param Rule $model
     * @param string $fieldsetId
     * @param string $formName
     * @return Form
     * @throws LocalizedException
     */
    protected function addTabToForm($model, $fieldsetId = 'conditions_fieldset', $formName = 'servicefee_rule_form')
    {
        if (!$model) {
            $id = $this->getRequest()->getParam('id');
            $model = $this->ruleFactory->create();
            $model->load($id);
        }
        $conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
        $newChildUrl = $this->getUrl(
            'sales_rule/promo_quote/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        /** @var Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $renderer = $this->getLayout()->createBlock(Fieldset::class);
        $renderer->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $newChildUrl
        )->setFieldSetId(
            $conditionsFieldSetId
        );

        $fieldset = $form->addFieldset(
            $fieldsetId,
            [
                'legend' => __(
                    'Apply the rule only if the following conditions are met (leave blank for all products).'
                )
            ]
        )->setRenderer(
            $renderer
        );
        $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'required' => true,
                'data-form-part' => $formName
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->_conditions
        );

        $form->setValues($model->getData());
        $this->setConditionFormName($model->getConditions(), $formName);
        return $form;
    }

    /**
     * Handles addition of form name to condition and its conditions.
     *
     * @param AbstractCondition $conditions
     * @param string $formName
     * @return void
     */
    private function setConditionFormName(AbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }
}
