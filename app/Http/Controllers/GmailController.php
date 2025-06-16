<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;

class GmailController extends Controller
{
    /**
     * Handle the incoming request to authorize Gmail API access.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authorize()
    {
        info('Gmail authorization started' . ' ' . 'GmailController authorize method');
        $client = new Client();
        $client->setApplicationName('Registration Application - Gmail API');
        $client->setScopes(Gmail::MAIL_GOOGLE_COM);
        $client->setAuthConfig(storage_path('credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setRedirectUri(route('gmail.callback'));

        $authUrl = $client->createAuthUrl();

        info('Redirecting to Gmail authorization URL: ' . $authUrl . ' ' . 'GmailController authorize method');
        return redirect($authUrl);
    }

    /**
     * Handle the callback from Gmail API after authorization.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        info('Gmail callback started' . ' ' . 'GmailController callback method');
        $client = new Client();
        $client->setApplicationName('Registration Application - Gmail API');
        $client->setScopes(Gmail::MAIL_GOOGLE_COM);
        $client->setAuthConfig(storage_path('credentials.json'));
        $client->setAccessType('offline');
        $client->setRedirectUri(route('gmail.callback'));

        $code = $request->get('code');
        $token = $client->fetchAccessTokenWithAuthCode($code);
        $client->setAccessToken($token);

        file_put_contents(storage_path('token.json'), json_encode($token));

        info('Gmail authorization successful, token saved.' . ' ' . 'GmailController callback method');
        return redirect()->route('gmail.send')->with('success', 'Authorization successful!');
    }

      protected function getClient()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('credentials.json'));
        $client->setRedirectUri(url('/oauth2callback'));
        $client->addScope(Gmail::GMAIL_SEND);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        if (session()->has('gmail_token')) {
            $client->setAccessToken(session('gmail_token'));

            // Refresh if expired
            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                session(['gmail_token' => $client->getAccessToken()]);
            }
        }

        return $client;
    }

    public function sendEmail()
    {
        $client = $this->getClient();

        if (!$client->getAccessToken()) {
            return redirect()->route('gmail.authorize');
        }

        $service = new Gmail($client);

        $rawMessage = "From: Your Name <your-email@gmail.com>\r\n";
        $rawMessage .= "To: arif14arif15@gmail.com\r\n";
        $rawMessage .= "Subject: Test Email from Laravel Gmail API\r\n";
        $rawMessage .= "\r\nThis is a test email sent using Gmail API from Laravel.";

        $mime = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');
        $message = new Message();
        $message->setRaw($mime);

        try {
            $service->users_messages->send("me", $message);
            return "✅ Email sent successfully!";
        } catch (\Exception $e) {
            return "❌ Error: " . $e->getMessage();
        }
    }

}
