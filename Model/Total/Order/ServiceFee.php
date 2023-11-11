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

namespace Igentu\ServiceFee\Model\Total\Order;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Total\AbstractTotal;

class ServiceFee extends AbstractTotal
{

    /**
     * @inheritDoc
     */
    public function collect(
        Order                       $order,
        ShippingAssignmentInterface $shippingAssignment,
        Total                       $total
    )
    {
        parent::collect($order, $shippingAssignment, $total);

        $total->setTotalAmount('service_fee', $order->getServiceFee())
            ->setBaseTotalAmount('service_fee', $order->getBaseServiceFee());

        return $this;
    }

    public function fetch(Order $order, Total $total)
    {
        if ($order->getServiceFee() > 0) {
            return [
                'code' => 'service_fee',
                'title' => __('Service Fee'),
                'value' => $order->getServiceFee()
            ];
        } else {
            return [];
        }
    }


    public function getLabel()
    {
        return __('Service Fee')->render();
    }
}

