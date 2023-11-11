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

namespace Igentu\ServiceFee\Controller\Adminhtml\Rule;

use Exception;
use Igentu\ServiceFee\Controller\Adminhtml\Rule;
use Igentu\ServiceFee\Model\RuleFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;

class Delete extends Rule
{

    /**
     * @var RedirectFactory $resultRedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param RuleFactory $ruleFactory
     * @param RedirectFactory $resultPageFactory
     */
    public function __construct(
        Context         $context,
        Registry        $coreRegistry,
        RuleFactory     $ruleFactory,
        RedirectFactory $resultRedirectFactory
    )
    {
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context, $coreRegistry, $ruleFactory);
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('rule_id');
        if ($id) {
            try {
                /** @var \Igentu\ServiceFee\Model\Rule $model */
                $model = $this->ruleFactory->create();
                $model->setId($id)->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the Rule.'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['rule_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Rule to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}

