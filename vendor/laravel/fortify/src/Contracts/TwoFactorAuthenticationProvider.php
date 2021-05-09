<?php

namespace Laravel\Fortify\Contracts;

interface TwoFactorAuthenticationProvider
{
    /**
     * Generate a new secret key.
     *
     * @return string
     */
    public function generateSecretKey();

    /**
     * Get the two factor authentication QR code URL.
     *
     * @param  string  $organisatorName
     * @param  string  $organisatorEmail
     * @param  string  $secret
     * @return string
     */
    public function qrCodeUrl($organisatorName, $organisatorEmail, $secret);

    /**
     * Verify the given token.
     *
     * @param  string  $secret
     * @param  string  $code
     * @return bool
     */
    public function verify($secret, $code);
}
