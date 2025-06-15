<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;

class GmailService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Registration Gmail API');
        $this->client->setScopes(Gmail::MAIL_GOOGLE_COM);
        $this->client->setAuthConfig(storage_path('credentials.json'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setRedirectUri(route('gmail.callback'));

        $tokenPath = storage_path('token.json');

        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);

            if ($this->client->isAccessTokenExpired()) {
                if ($this->client->getRefreshToken()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
                }
            }
        }
    }

    public function send($to, $subject, $body)
    {
        $service = new Gmail($this->client);

        $strRawMessage = "To: {$to}\r\n";
        $strRawMessage .= "Subject: {$subject}\r\n";
        $strRawMessage .= "From: Me <arif.fullstackdev@gmail.com>\r\n";
        $strRawMessage .= "\r\n{$body}";

        $mime = rtrim(strtr(base64_encode($strRawMessage), '+/', '-_'), '=');
        $message = new Message();
        $message->setRaw($mime);

        $service->users_messages->send('me', $message);
    }
}

