<?php

namespace jsoner;


class Resolver
{
    private $config;

    /**
     * Resolver constructor.
     * @param \GlobalVarConfig $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function resolve($url)
    {
        $ch = curl_init();
        $user = $this->config->get("User");
        $pass = $this->config->get("Pass");

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_USERPWD => "$user:$pass",
            CURLOPT_HTTPHEADER => ["Accept: application/json",],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $error_message = curl_error($ch);
        $error_code = curl_errno($ch);

        curl_close($ch);

        if ($response === false) {
            throw new \CurlException($error_message, $error_code);
        }

        return $response;
    }
}
