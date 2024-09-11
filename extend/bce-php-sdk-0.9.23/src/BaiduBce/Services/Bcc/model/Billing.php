<?php
/*
* Copyright 2017 Baidu, Inc.
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/

namespace BaiduBce\Services\Bcc\model;

class Billing
{
    var $paymentTiming;
    var $reservation;

    /**
     * Billing constructor.
     * @param null $paymentTiming
     *      The pay time of the payment,
     *      see more detail in https://bce.baidu.com/doc/BCC/API.html#Billing
     * @param int $reservationLength
     *      The duration to buy in specified time unit,
     *      available values are [1,2,3,4,5,6,7,8,9,12,24,36] now.
     * @param string $reservationTimeUnit
     *      The time unit to specify the duration ,only "Month" can be used now.
     */
    function __construct($paymentTiming=null, $reservationLength=1, $reservationTimeUnit='Month')
    {
        if ($paymentTiming !== null) {
            $this->paymentTiming = $paymentTiming;
        }
        $this->reservation = new Reservation($reservationLength, $reservationTimeUnit);
    }
}