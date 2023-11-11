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

namespace Igentu\ServiceFee\Helper;

use Igentu\ServiceFee\Model\ResourceModel\Rule\Collection;
use Igentu\ServiceFee\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Igentu\ServiceFee\Model\Rule;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    private RuleCollectionFactory $ruleCollectionFactory;

    /**
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param Context $context
     */
    public function __construct(
        RuleCollectionFactory $ruleCollectionFactory,
        Context               $context
    )
    {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Validates if the quote is eligible for service fees and apply them
     * @param $quote
     */
    public function applyServiceFeesToQuote($quote)
    {
        $ids = [];
        $amount = 0;
        /** @var Collection $rules */
        $rules = $this->getActiveServiceFeeRules();
        /** @var Rule $rule */
        foreach ($rules as $rule) {
            if ($rule->validate($quote->setQuote($quote))) {
                $ids[] = $rule->getId();
                $amount += $rule->getBaseServiceFee();
            }
        }
        $quote->setAppliedServiceFeeIds(implode(',', $ids));
        $quote->setServiceFee($amount);
        $quote->setBaseServiceFee($amount);
    }

    public function getActiveServiceFeeRules(): Collection
    {
        return $this->ruleCollectionFactory->create()->addFieldToFilter('is_active', 1);
    }

}