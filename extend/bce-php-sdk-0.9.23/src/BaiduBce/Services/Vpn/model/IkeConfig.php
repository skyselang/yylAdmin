<?php


namespace BaiduBce\Services\Vpn\model;


class IkeConfig
{
    // Version, value range ：v1/v2
    var $ikeVersion;
    // Negotiation mode, value range ：main/aggressive
    var $ikeMode;
    // Encryption algorithm, value range ：aes/aes192/aes256/3des
    var $ikeEncAlg;
    // Authentication algorithm, value range ：sha1/md5
    var $ikeAuthAlg;
    //DH Grouping, value range ：group2/group5/group14/group24
    var $ikePfs;
    // SA Life cycle, value range ：60-86400
    var $ikeLifeTime;

    /**
     * @return mixed
     */
    public function getIkeVersion()
    {
        return $this->ikeVersion;
    }

    /**
     * @param mixed $ikeVersion
     */
    public function setIkeVersion($ikeVersion): void
    {
        $this->ikeVersion = $ikeVersion;
    }

    /**
     * @return mixed
     */
    public function getIkeMode()
    {
        return $this->ikeMode;
    }

    /**
     * @param mixed $ikeMode
     */
    public function setIkeMode($ikeMode): void
    {
        $this->ikeMode = $ikeMode;
    }

    /**
     * @return mixed
     */
    public function getIkeEncAlg()
    {
        return $this->ikeEncAlg;
    }

    /**
     * @param mixed $ikeEncAlg
     */
    public function setIkeEncAlg($ikeEncAlg): void
    {
        $this->ikeEncAlg = $ikeEncAlg;
    }

    /**
     * @return mixed
     */
    public function getIkeAuthAlg()
    {
        return $this->ikeAuthAlg;
    }

    /**
     * @param mixed $ikeAuthAlg
     */
    public function setIkeAuthAlg($ikeAuthAlg): void
    {
        $this->ikeAuthAlg = $ikeAuthAlg;
    }

    /**
     * @return mixed
     */
    public function getIkePfs()
    {
        return $this->ikePfs;
    }

    /**
     * @param mixed $ikePfs
     */
    public function setIkePfs($ikePfs): void
    {
        $this->ikePfs = $ikePfs;
    }

    /**
     * @return mixed
     */
    public function getIkeLifeTime()
    {
        return $this->ikeLifeTime;
    }

    /**
     * @param mixed $ikeLifeTime
     */
    public function setIkeLifeTime($ikeLifeTime): void
    {
        $this->ikeLifeTime = $ikeLifeTime;
    }


}