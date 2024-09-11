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

class SecurityGroupRuleModel
{
    var $remark;
    var $direction;
    var $ethertype;
    var $portRange;
    var $protocol;
    var $sourceGroupId;
    var $sourceIp;
    var $destGroupId;
    var $destIp;
    var $securityGroupId;

    /**
     * This class define the rule of the securitygroup.
     *
     * @param string $remark
     *          The remark for the rule.
     *
     * @param string $direction
     *          The parameter to define the rule direction,available value are "ingress/egress".
     *
     * @param string $ethertype
     *          The ethernet protocol.
     *
     * @param string $portRange
     *          The port range to specify the port which the rule will work on.
     *          Available range is rang [0, 65535], the fault value is "" for all port.
     *
     * @param string $protocol
     *          The parameter specify which protocol will the rule work on, the fault value is "" for all protocol.
     *          Available protocol are tcp, udp and icmp.
     *
     * @param string $sourceGroupId
     *          The id of source securitygroup.
     *          Only works for direction = "ingress".
     *
     * @param string $sourceIp
     *          The source ip range with CIDR formats. The default value 0.0.0.0/0 (allow all ip address),
     *          other supported formats such as 10.159.6.18/12 or 10.159.6.186. Only supports IPV4.
     *          Only works for  direction = "ingress".
     *
     * @param string $destGroupId
     *          The id of destination securitygroup.
     *          Only works for  direction = "egress".
     *
     * @param string $destIp
     *          The destination ip range with CIDR formats. The default value 0.0.0.0/0 (allow all ip address),
     *          other supported formats such as 10.159.6.18/12 or 10.159.6.186. Only supports IPV4.
     *          Only works for  direction = "egress".
     *
     * @param string $securityGroupId
     *          The id of the securitygroup for the rule.
     */
    function __construct($remark=null, $direction=null, $ethertype=null, $portRange=null, $protocol=null,
                         $sourceGroupId=null, $sourceIp=null, $destGroupId=null, $destIp=null, $securityGroupId=null)
    {
        $this->remark = $remark;
        $this->direction = $direction;
        $this->ethertype = $ethertype;
        $this->portRange = $portRange;
        $this->protocol = $protocol;
        $this->sourceGroupId = $sourceGroupId;
        $this->sourceIp = $sourceIp;
        $this->destGroupId = $destGroupId;
        $this->destIp = $destIp;
        $this->securityGroupId = $securityGroupId;
    }
}