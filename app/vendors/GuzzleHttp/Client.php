<?php
class GuzzleHttp_Client {
    private $base_uri;
    private $headers;

    public function __construct($config = array()) {
        $this->base_uri = isset($config['base_uri']) ? $config['base_uri'] : '';
        $this->headers = isset($config['headers']) ? $config['headers'] : array();
    }

    public function post($path, $options = array()) {
        $url = $this->base_uri . $path;
        $payload = isset($options['json']) ? json_encode($options['json']) : '';
        $headers = array_merge($this->headers, array(
            'Content-Length: ' . strlen($payload)
        ));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(function ($k, $v) {
            return "$k: $v";
        }, array_keys($headers), $headers));

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status >= 400) {
            throw new Exception("Error HTTP $status: $response");
        }

        return (object)[ 'getStatusCode' => $status ];
    }
}
?>
