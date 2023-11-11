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
use Igentu\ServiceFee\Model\Rule;
use Igentu\ServiceFee\Model\RuleFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

class Save extends \Igentu\ServiceFee\Controller\Adminhtml\Rule
{
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var RedirectFactory $resultRedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param RuleFactory $ruleFactory
     * @param DataPersistorInterface $dataPersistor
     * @param RedirectFactory $resultPageFactory
     */
    public function __construct(
        Context                $context,
        Registry               $coreRegistry,
        RuleFactory            $ruleFactory,
        DataPersistorInterface $dataPersistor,
        RedirectFactory        $resultRedirectFactory
    )
    {
        $this->dataPersistor = $dataPersistor;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context, $coreRegistry, $ruleFactory);
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('rule_id');
            /** @var Rule $model */
            $model = $this->ruleFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Rule no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }

            unset($data['rule']);
            $model->loadPost($data);
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Rule.'));
                $this->dataPersistor->clear('servicefee_rule');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['rule_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Rule.'));
            }

            $this->dataPersistor->set('servicefee_rule', $data);
            return $resultRedirect->setPath('*/*/edit', ['rule_id' => $this->getRequest()->getParam('rule_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

