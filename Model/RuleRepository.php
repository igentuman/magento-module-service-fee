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

use Exception;
use Igentu\ServiceFee\Api\Data\RuleInterface;
use Igentu\ServiceFee\Api\Data\RuleInterfaceFactory;
use Igentu\ServiceFee\Api\Data\RuleSearchResultsInterfaceFactory;
use Igentu\ServiceFee\Api\RuleRepositoryInterface;
use Igentu\ServiceFee\Model\ResourceModel\Rule as ResourceRule;
use Igentu\ServiceFee\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class RuleRepository implements RuleRepositoryInterface
{
    protected ResourceRule $resource;

    protected RuleInterfaceFactory $ruleFactory;

    protected RuleCollectionFactory $ruleCollectionFactory;

    protected Rule $searchResultsFactory;

    protected CollectionProcessorInterface $collectionProcessor;


    /**
     * @param ResourceRule $resource
     * @param RuleInterfaceFactory $ruleFactory
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param RuleSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceRule                      $resource,
        RuleInterfaceFactory              $ruleFactory,
        RuleCollectionFactory             $ruleCollectionFactory,
        RuleSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface      $collectionProcessor
    )
    {
        $this->resource = $resource;
        $this->ruleFactory = $ruleFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(RuleInterface $rule)
    {
        try {
            $this->resource->save($rule);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the rule: %1',
                $exception->getMessage()
            ));
        }
        return $rule;
    }

    /**
     * @inheritDoc
     */
    public function get($ruleId)
    {
        $rule = $this->ruleFactory->create();
        $this->resource->load($rule, $ruleId);
        if (!$rule->getId()) {
            throw new NoSuchEntityException(__('Rule with id "%1" does not exist.', $ruleId));
        }
        return $rule;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->ruleCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(RuleInterface $rule)
    {
        try {
            $ruleModel = $this->ruleFactory->create();
            $this->resource->load($ruleModel, $rule->getRuleId());
            $this->resource->delete($ruleModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Rule: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($ruleId)
    {
        return $this->delete($this->get($ruleId));
    }
}

