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

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'
], function (Component, quote, priceUtils, totals) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Igentu_ServiceFee/checkout/summary/servicefee'
        },

        totals: quote.getTotals(),
        hasTotal: function () {
            if (this.totals()) {
                return !!totals.getSegment('service_fee');
            }

            return false;
        },
        isDisplayed: function () {
            return this.isFullMode();
        },
        getValue: function () {
            return this.getFormattedPrice(this.hasTotal() ? totals.getSegment('service_fee').value : 0);
        },
        getBaseValue: function () {
            return priceUtils.formatPrice(this.hasTotal() ? this.totals().base_service_fee : 0, quote.getBasePriceFormat());
        },
        shouldDisplay: function () {
            return this.hasTotal();
        }
    });
});
