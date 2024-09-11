<?php
/*
* Copyright 2023 Baidu, Inc.
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

namespace BaiduBce\Services\Sms\model;

class StatisticsResult {
    private $datetime;
    private $countryAlpha2Code;
    private $submitCount;
    private $submitLongCount;
    private $responseSuccessCount;
    private $responseSuccessProportion;
    private $deliverSuccessCount;
    private $deliverSuccessLongCount;
    private $deliverSuccessProportion;
    private $deliverFailureCount;
    private $deliverFailureProportion;
    private $receiptProportion;
    private $unknownCount;
    private $unknownProportion;
    private $responseTimeoutCount;
    private $unknownErrorCount;
    private $notExistCount;
    private $signatureOrTemplateCount;
    private $abnormalCount;
    private $overclockingCount;
    private $otherErrorCount;
    private $blacklistCount;
    private $routeErrorCount;
    private $issueFailureCount;
    private $parameterErrorCount;
    private $illegalWordCount;
    private $anomalyCount;

    /**
     * @param $datetime
     * @param $countryAlpha2Code
     * @param $submitCount
     * @param $submitLongCount
     * @param $responseSuccessCount
     * @param $responseSuccessProportion
     * @param $deliverSuccessCount
     * @param $deliverSuccessLongCount
     * @param $deliverSuccessProportion
     * @param $deliverFailureCount
     * @param $deliverFailureProportion
     * @param $receiptProportion
     * @param $unknownCount
     * @param $unknownProportion
     * @param $responseTimeoutCount
     * @param $unknownErrorCount
     * @param $notExistCount
     * @param $signatureOrTemplateCount
     * @param $abnormalCount
     * @param $overclockingCount
     * @param $otherErrorCount
     * @param $blacklistCount
     * @param $routeErrorCount
     * @param $issueFailureCount
     * @param $parameterErrorCount
     * @param $illegalWordCount
     * @param $anomalyCount
     */
    public function __construct($result)
    {
        $this->datetime = $result->datetime;
        $this->countryAlpha2Code = $result->countryAlpha2Code;
        $this->submitCount = $result->submitCount;
        $this->submitLongCount = $result->submitLongCount;
        $this->responseSuccessCount = $result->responseSuccessCount;
        $this->responseSuccessProportion = $result->responseSuccessProportion;
        $this->deliverSuccessCount = $result->deliverSuccessCount;
        $this->deliverSuccessLongCount = $result->deliverSuccessLongCount;
        $this->deliverSuccessProportion = $result->deliverSuccessProportion;
        $this->deliverFailureCount = $result->deliverFailureCount;
        $this->deliverFailureProportion = $result->deliverFailureProportion;
        $this->receiptProportion = $result->receiptProportion;
        $this->unknownCount = $result->unknownCount;
        $this->unknownProportion = $result->unknownProportion;
        $this->responseTimeoutCount = $result->responseTimeoutCount;
        $this->unknownErrorCount = $result->unknownErrorCount;
        $this->notExistCount = $result->notExistCount;
        $this->signatureOrTemplateCount = $result->signatureOrTemplateCount;
        $this->abnormalCount = $result->abnormalCount;
        $this->overclockingCount = $result->overclockingCount;
        $this->otherErrorCount = $result->otherErrorCount;
        $this->blacklistCount = $result->blacklistCount;
        $this->routeErrorCount = $result->routeErrorCount;
        $this->issueFailureCount = $result->issueFailureCount;
        $this->parameterErrorCount = $result->parameterErrorCount;
        $this->illegalWordCount = $result->illegalWordCount;
        $this->anomalyCount = $result->anomalyCount;
    }


    /**
     * @return mixed
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param mixed $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * @return mixed
     */
    public function getCountryAlpha2Code()
    {
        return $this->countryAlpha2Code;
    }

    /**
     * @param mixed $countryAlpha2Code
     */
    public function setCountryAlpha2Code($countryAlpha2Code)
    {
        $this->countryAlpha2Code = $countryAlpha2Code;
    }

    /**
     * @return mixed
     */
    public function getSubmitCount()
    {
        return $this->submitCount;
    }

    /**
     * @param mixed $submitCount
     */
    public function setSubmitCount($submitCount)
    {
        $this->submitCount = $submitCount;
    }

    /**
     * @return mixed
     */
    public function getSubmitLongCount()
    {
        return $this->submitLongCount;
    }

    /**
     * @param mixed $submitLongCount
     */
    public function setSubmitLongCount($submitLongCount)
    {
        $this->submitLongCount = $submitLongCount;
    }

    /**
     * @return mixed
     */
    public function getResponseSuccessCount()
    {
        return $this->responseSuccessCount;
    }

    /**
     * @param mixed $responseSuccessCount
     */
    public function setResponseSuccessCount($responseSuccessCount)
    {
        $this->responseSuccessCount = $responseSuccessCount;
    }

    /**
     * @return mixed
     */
    public function getResponseSuccessProportion()
    {
        return $this->responseSuccessProportion;
    }

    /**
     * @param mixed $responseSuccessProportion
     */
    public function setResponseSuccessProportion($responseSuccessProportion)
    {
        $this->responseSuccessProportion = $responseSuccessProportion;
    }

    /**
     * @return mixed
     */
    public function getDeliverSuccessCount()
    {
        return $this->deliverSuccessCount;
    }

    /**
     * @param mixed $deliverSuccessCount
     */
    public function setDeliverSuccessCount($deliverSuccessCount)
    {
        $this->deliverSuccessCount = $deliverSuccessCount;
    }

    /**
     * @return mixed
     */
    public function getDeliverSuccessLongCount()
    {
        return $this->deliverSuccessLongCount;
    }

    /**
     * @param mixed $deliverSuccessLongCount
     */
    public function setDeliverSuccessLongCount($deliverSuccessLongCount)
    {
        $this->deliverSuccessLongCount = $deliverSuccessLongCount;
    }

    /**
     * @return mixed
     */
    public function getDeliverSuccessProportion()
    {
        return $this->deliverSuccessProportion;
    }

    /**
     * @param mixed $deliverSuccessProportion
     */
    public function setDeliverSuccessProportion($deliverSuccessProportion)
    {
        $this->deliverSuccessProportion = $deliverSuccessProportion;
    }

    /**
     * @return mixed
     */
    public function getDeliverFailureCount()
    {
        return $this->deliverFailureCount;
    }

    /**
     * @param mixed $deliverFailureCount
     */
    public function setDeliverFailureCount($deliverFailureCount)
    {
        $this->deliverFailureCount = $deliverFailureCount;
    }

    /**
     * @return mixed
     */
    public function getDeliverFailureProportion()
    {
        return $this->deliverFailureProportion;
    }

    /**
     * @param mixed $deliverFailureProportion
     */
    public function setDeliverFailureProportion($deliverFailureProportion)
    {
        $this->deliverFailureProportion = $deliverFailureProportion;
    }

    /**
     * @return mixed
     */
    public function getReceiptProportion()
    {
        return $this->receiptProportion;
    }

    /**
     * @param mixed $receiptProportion
     */
    public function setReceiptProportion($receiptProportion)
    {
        $this->receiptProportion = $receiptProportion;
    }

    /**
     * @return mixed
     */
    public function getUnknownCount()
    {
        return $this->unknownCount;
    }

    /**
     * @param mixed $unknownCount
     */
    public function setUnknownCount($unknownCount)
    {
        $this->unknownCount = $unknownCount;
    }

    /**
     * @return mixed
     */
    public function getUnknownProportion()
    {
        return $this->unknownProportion;
    }

    /**
     * @param mixed $unknownProportion
     */
    public function setUnknownProportion($unknownProportion)
    {
        $this->unknownProportion = $unknownProportion;
    }

    /**
     * @return mixed
     */
    public function getResponseTimeoutCount()
    {
        return $this->responseTimeoutCount;
    }

    /**
     * @param mixed $responseTimeoutCount
     */
    public function setResponseTimeoutCount($responseTimeoutCount)
    {
        $this->responseTimeoutCount = $responseTimeoutCount;
    }

    /**
     * @return mixed
     */
    public function getUnknownErrorCount()
    {
        return $this->unknownErrorCount;
    }

    /**
     * @param mixed $unknownErrorCount
     */
    public function setUnknownErrorCount($unknownErrorCount)
    {
        $this->unknownErrorCount = $unknownErrorCount;
    }

    /**
     * @return mixed
     */
    public function getNotExistCount()
    {
        return $this->notExistCount;
    }

    /**
     * @param mixed $notExistCount
     */
    public function setNotExistCount($notExistCount)
    {
        $this->notExistCount = $notExistCount;
    }

    /**
     * @return mixed
     */
    public function getSignatureOrTemplateCount()
    {
        return $this->signatureOrTemplateCount;
    }

    /**
     * @param mixed $signatureOrTemplateCount
     */
    public function setSignatureOrTemplateCount($signatureOrTemplateCount)
    {
        $this->signatureOrTemplateCount = $signatureOrTemplateCount;
    }

    /**
     * @return mixed
     */
    public function getAbnormalCount()
    {
        return $this->abnormalCount;
    }

    /**
     * @param mixed $abnormalCount
     */
    public function setAbnormalCount($abnormalCount)
    {
        $this->abnormalCount = $abnormalCount;
    }

    /**
     * @return mixed
     */
    public function getOverclockingCount()
    {
        return $this->overclockingCount;
    }

    /**
     * @param mixed $overclockingCount
     */
    public function setOverclockingCount($overclockingCount)
    {
        $this->overclockingCount = $overclockingCount;
    }

    /**
     * @return mixed
     */
    public function getOtherErrorCount()
    {
        return $this->otherErrorCount;
    }

    /**
     * @param mixed $otherErrorCount
     */
    public function setOtherErrorCount($otherErrorCount)
    {
        $this->otherErrorCount = $otherErrorCount;
    }

    /**
     * @return mixed
     */
    public function getBlacklistCount()
    {
        return $this->blacklistCount;
    }

    /**
     * @param mixed $blacklistCount
     */
    public function setBlacklistCount($blacklistCount)
    {
        $this->blacklistCount = $blacklistCount;
    }

    /**
     * @return mixed
     */
    public function getRouteErrorCount()
    {
        return $this->routeErrorCount;
    }

    /**
     * @param mixed $routeErrorCount
     */
    public function setRouteErrorCount($routeErrorCount)
    {
        $this->routeErrorCount = $routeErrorCount;
    }

    /**
     * @return mixed
     */
    public function getIssueFailureCount()
    {
        return $this->issueFailureCount;
    }

    /**
     * @param mixed $issueFailureCount
     */
    public function setIssueFailureCount($issueFailureCount)
    {
        $this->issueFailureCount = $issueFailureCount;
    }

    /**
     * @return mixed
     */
    public function getParameterErrorCount()
    {
        return $this->parameterErrorCount;
    }

    /**
     * @param mixed $parameterErrorCount
     */
    public function setParameterErrorCount($parameterErrorCount)
    {
        $this->parameterErrorCount = $parameterErrorCount;
    }

    /**
     * @return mixed
     */
    public function getIllegalWordCount()
    {
        return $this->illegalWordCount;
    }

    /**
     * @param mixed $illegalWordCount
     */
    public function setIllegalWordCount($illegalWordCount)
    {
        $this->illegalWordCount = $illegalWordCount;
    }

    /**
     * @return mixed
     */
    public function getAnomalyCount()
    {
        return $this->anomalyCount;
    }

    /**
     * @param mixed $anomalyCount
     */
    public function setAnomalyCount($anomalyCount)
    {
        $this->anomalyCount = $anomalyCount;
    }




}