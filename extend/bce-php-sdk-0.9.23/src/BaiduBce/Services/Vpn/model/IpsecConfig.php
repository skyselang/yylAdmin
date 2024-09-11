<?php


namespace BaiduBce\Services\Vpn\model;


class IpsecConfig
{
    // Encryption algorithm, value range ：aes/aes192/aes256/3des
    var $ipsecEncAlg;
    // Authentication algorithm, value range ：sha1/md5
    var $ipsecAuthAlg;
    // DH Grouping, value range ：group2/group5/group14/group24
    var $ipsecPfs;
    // SA Life cycle, value range ：180-86400
    var $ipsecLifetime;

    /**
     * @return mixed
     */
    public function getIpsecEncAlg()
    {
        return $this->ipsecEncAlg;
    }

    /**
     * @param mixed $ipsecEncAlg
     */
    public function setIpsecEncAlg($ipsecEncAlg): void
    {
        $this->ipsecEncAlg = $ipsecEncAlg;
    }

    /**
     * @return mixed
     */
    public function getIpsecAuthAlg()
    {
        return $this->ipsecAuthAlg;
    }

    /**
     * @param mixed $ipsecAuthAlg
     */
    public function setIpsecAuthAlg($ipsecAuthAlg): void
    {
        $this->ipsecAuthAlg = $ipsecAuthAlg;
    }

    /**
     * @return mixed
     */
    public function getIpsecPfs()
    {
        return $this->ipsecPfs;
    }

    /**
     * @param mixed $ipsecPfs
     */
    public function setIpsecPfs($ipsecPfs): void
    {
        $this->ipsecPfs = $ipsecPfs;
    }

    /**
     * @return mixed
     */
    public function getIpsecLifetime()
    {
        return $this->ipsecLifetime;
    }

    /**
     * @param mixed $ipsecLifetime
     */
    public function setIpsecLifetime($ipsecLifetime): void
    {
        $this->ipsecLifetime = $ipsecLifetime;
    }


}