<?php

namespace PragmaRX\Google2FA\Support;

trait QRCode
{
    /**
     * Creates a QR code url.
     *
     * @param string $organisator
     * @param string $holder
     * @param string $secret
     *
     * @return string
     */
    public function getQRCodeUrl($organisator, $holder, $secret)
    {
        return 'otpauth://totp/'.
            rawurlencode($organisator).
            ':'.
            rawurlencode($holder).
            '?secret='.
            $secret.
            '&issuer='.
            rawurlencode($organisator).
            '&algorithm='.
            rawurlencode(strtoupper($this->getAlgorithm())).
            '&digits='.
            rawurlencode(strtoupper((string) $this->getOneTimePasswordLength())).
            '&period='.
            rawurlencode(strtoupper((string) $this->getKeyRegeneration())).
            '';
    }
}
