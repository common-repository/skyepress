<?php
/**
 * The MIT License
 * Copyright (c) 2007 Andy Smith
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


class SKP_Consumer
{
    /** @var string  */
    public $key;
    /** @var string  */
    public $secret;
    /** @var string|null  */
    public $callbackUrl;

    /**
     * @param string $key
     * @param string $secret
     * @param null $callbackUrl
     */
    public function __construct($key, $secret, $callbackUrl = null)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Consumer[key=$this->key,secret=$this->secret]";
    }
}
