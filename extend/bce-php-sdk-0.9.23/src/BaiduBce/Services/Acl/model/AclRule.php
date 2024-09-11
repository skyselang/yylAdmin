<?php

namespace BaiduBce\Services\Acl\model;

class AclRule
{
    var $subnetId;
    var $description;
    var $protocol;
    var $sourceIpAddress;
    var $destinationIpAddress;
    var $sourcePort;
    var $destinationPort;
    var $position;
    var $direction;
    var $action;

    /**
     * AclRule information.
     *
     * @param string $subnetId
     *          The acl rule's subnetId .
     *
     * @param string $description
     *          The acl rule's description .The optional parameter.
     *
     * @param string $protocol
     *          The acl rule's protocol .
     *
     * @param string $sourceIpAddress
     *          The acl rule's sourceIpAddress .
     *
     * @param string $destinationIpAddress
     *          The acl rule's destinationIpAddress .
     *
     * @param string $sourcePort
     *          The acl rule's sourcePort .
     *
     * @param string $destinationPort
     *          The acl rule's destinationPort .
     *
     * @param string $position
     *          The acl rule's position .
     *
     * @param string $direction
     *          The acl rule's direction .
     *
     * @param string $action
     *           The acl rule's action .
     */
    function __construct($subnetId, $protocol, $sourceIpAddress, $destinationIpAddress, $sourcePort, $destinationPort, $position, $direction, $action, $description = null)
    {
        $this->subnetId = $subnetId;
        $this->protocol = $protocol;
        $this->sourceIpAddress = $sourceIpAddress;
        $this->destinationIpAddress = $destinationIpAddress;
        $this->sourcePort = $sourcePort;
        $this->destinationPort = $destinationPort;
        $this->position = $position;
        $this->direction = $direction;
        $this->action = $action;
        $this->description = $description;
    }
}