<?php
App::import('Vendor', 'GuzzleHttp_Client', array('file' => 'GuzzleHttp/Client.php'));

class Mailer {
    private $apiKey;
    private $templateId;
    private $client;

    public function __construct($apiKey, $templateId = 1) {
        $this->apiKey = $apiKey;
        $this->templateId = $templateId;
        $this->client = new GuzzleHttp_Client(array(
            'base_uri' => 'https://api.brevo.com/v3/',
            'headers' => array(
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            )
        ));
    }

    public function sendEmailTemplate($toEmail, $toName, $params = array()) {
        $subject = isset($params['subject']) ? $params['subject'] : null;
        if ($subject) {
            unset($params['subject']);
        }

        $payload = array(
            'to' => array(array('email' => $toEmail, 'name' => $toName)),
            'templateId' => $this->templateId,
            'params' => $params
        );
        if ($subject) {
            $payload['subject'] = $subject;
        }

        try {
            $response = $this->client->post('smtp/email', array(
                'json' => $payload
            ));

            $status = isset($response->statusCode) ? $response->statusCode : 0;
            $success = ($status == 201);

            $this->logEmailSend($toEmail, $subject, $status, $success);
            return $status;

        } catch (Exception $e) {
            $this->logEmailSend($toEmail, $subject, 0, false, $e->getMessage());
            return 0;
        }
    }

    
    private function logEmailSend($email, $subject, $statusCode, $success, $errorMessage = '') {
        $statusText = $success ? 'ENVIADO' : 'ERROR';
        $fecha = date('Y-m-d H:i:s');
        $line = "[$fecha] [$statusText] [$statusCode] $email :: $subject";

        if (!$success && $errorMessage) {
            $line .= " :: Error: $errorMessage";
        }

        $ruta = APP . 'tmp' . DS . 'logs' . DS . 'mailer_'.date('Ymd').'.log';
        file_put_contents($ruta, $line . "\n", FILE_APPEND);
    }
    


}
?>
