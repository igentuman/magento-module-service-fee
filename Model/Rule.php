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

namespace Igentu\ServiceFee\Model;

use Igentu\ServiceFee\Api\Data\RuleInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Rule\Model\AbstractModel;
use Magento\SalesRule\Api\Data\ConditionInterface;
use Magento\SalesRule\Model\Rule\Condition\CombineFactory;
use Magento\Store\Model\StoreManagerInterface;

class Rule extends AbstractModel implements RuleInterface
{
    protected CombineFactory $_condCombineFactory;
    protected StoreManagerInterface|AbstractDb $_storeManager;

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    protected $_validatedAddresses = [];

    /**
     * @inheritDoc
     */
    public function __construct(
        Context                    $context,
        Registry                   $registry,
        FormFactory                $formFactory,
        TimezoneInterface          $localeDate,
        CombineFactory             $condCombineFactory,
        StoreManagerInterface      $storeManager,
        AbstractResource           $resource = null,
        AbstractDb                 $resourceCollection = null,
        array                      $data = [],
        ExtensionAttributesFactory $extensionFactory = null,
        AttributeValueFactory      $customAttributeFactory = null,
        Json                       $serializer = null
    )
    {
        $this->_condCombineFactory = $condCombineFactory;
        $this->_storeManager = $storeManager;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data,
            $extensionFactory,
            $customAttributeFactory,
            $serializer
        );
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Rule::class);
        $this->setIdFieldName('rule_id');
    }

    /**
     * @inheritDoc
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setIsActive($status)
    {
        return $this->setData(self::IS_ACTIVE, $status);
    }


    /**
     * @inheritDoc
     */
    public function getBaseServiceFee()
    {
        return $this->getData(self::BASE_SERVICE_FEE);
    }

    /**
     * @inheritDoc
     */
    public function setBaseServiceFee($amount)
    {
        return $this->setData(self::BASE_SERVICE_FEE, $amount);
    }

    public function getConditionsInstance()
    {
        return $this->_condCombineFactory->create();
    }

    public function getActionsInstance()
    {
        return null;
    }

    public function getCondition()
    {
        parent::getConditions();
    }

    public function setCondition(ConditionInterface $condition = null)
    {
        parent::setConditions($condition);
    }

    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * Check cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     */
    public function hasIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param Address $address
     * @param bool $validationResult
     * @return $this
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->_getAddressId($address);
        $this->_validatedAddresses[$addressId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->_validatedAddresses[$addressId]) ? $this->_validatedAddresses[$addressId] : false;
    }

    /**
     * Return id for address
     *
     * @param Address $address
     * @return string
     */
    private function _getAddressId($address)
    {
        if ($address instanceof Address) {
            return $address->getId();
        }
        return $address;
    }

    protected function _resetActions($actions = null)
    {
        return $this;
    }
}

