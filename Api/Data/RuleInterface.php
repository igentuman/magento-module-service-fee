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

namespace Igentu\ServiceFee\Api\Data;

use Magento\SalesRule\Api\Data\ConditionInterface;

interface RuleInterface
{
    const NAME = 'rule';
    const IS_ACTIVE = 'is_active';
    const RULE_ID = 'rule_id';
    const BASE_SERVICE_FEE = 'base_service_fee';

    /**
     * Get rule_id
     * @return string|null
     */
    public function getRuleId();

    /**
     * Set rule_id
     * @param string $ruleId
     * @return RuleInterface
     */
    public function setRuleId($ruleId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return RuleInterface
     */
    public function setName($name);

    /**
     * Get status
     * @return int|null
     */
    public function getIsActive();

    /**
     * Set status
     * @param int $status
     * @return RuleInterface
     */
    public function setIsActive($status);

    /**
     * Get amount
     * @return int|null
     */
    public function getBaseServiceFee();

    /**
     * Set amount
     * @param int $amount
     * @return RuleInterface
     */
    public function setBaseServiceFee($amount);

    /**
     * Get condition for the rule
     *
     * @return ConditionInterface|null
     */
    public function getCondition();

    /**
     * Set condition for the rule
     *
     * @param ConditionInterface|null $condition
     * @return $this
     */
    public function setCondition(ConditionInterface $condition = null);

}

