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

class EphemeralDisk
{
    var $sizeInGB;
    var $storageType;

    /**
     * This class define detail of creating ephemeral volume.
     *
     * @param int $sizeInGB
     *          The size of volume in GB.
     *
     * @param string $storageType
     *          The storage type of volume,
     *          see more detail in https://bce.baidu.com/doc/BCC/API.html#StorageType
     */
    function __construct($sizeInGB, $storageType='sata')
    {
        $this->storageType = $storageType;
        $this->sizeInGB = $sizeInGB;
    }
}