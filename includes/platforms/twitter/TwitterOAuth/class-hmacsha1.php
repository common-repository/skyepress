<?php
/**
 * The MIT License
 * Copyright (c) 2007 Andy Smith
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The HMAC-SHA1 signature method uses the HMAC-SHA1 signature algorithm as defined in [RFC2104]
 * where the Signature Base String is the text and the key is the concatenated values (each first
 * encoded per Parameter Encoding) of the Consumer Secret and Token Secret, separated by an '&'
 * character (ASCII code 38) even if empty.
 *   - Chapter 9.2 ("HMAC-SHA1")
 */
class SKP_HmacSha1 extends SKP_SignatureMethod
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return "HMAC-SHA1";
    }

    /**
     * {@inheritDoc}
     */
    public function buildSignature(SKP_Request $request, SKP_Consumer $consumer, SKP_Token $token = null)
    {
        $signatureBase = $request->getSignatureBaseString();

        $parts = [$consumer->secret, null !== $token ? $token->secret : ""];

        $parts = SKP_Util::urlencodeRfc3986($parts);
        $key = implode('&', $parts);

        return base64_encode(hash_hmac('sha1', $signatureBase, $key, true));
    }
}
