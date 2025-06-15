<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Gmail;

class GmailController extends Controller
{
    public function authorize()
    {
        $client = new Client();
        $client->setApplicationName('Gmail API PHP Quickstart');
        $client->setScopes(Gmail::MAIL_GOOGLE_COM);
        $client->setAuthConfig(storage_path('credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setRedirectUri(route('gmail.callback'));

        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        $client = new Client();
        $client->setApplicationName('Gmail API PHP Quickstart');
        $client->setScopes(Gmail::MAIL_GOOGLE_COM);
        $client->setAuthConfig(storage_path('credentials.json'));
        $client->setAccessType('offline');
        $client->setRedirectUri(route('gmail.callback'));

        $code = $request->get('code');
        $token = $client->fetchAccessTokenWithAuthCode($code);
        $client->setAccessToken($token);

        file_put_contents(storage_path('token.json'), json_encode($token));

        return redirect()->route('gmail.send')->with('success', 'Authorization successful!');
    }

    public function getClient()
    {
        $client = new Client();
        $client->setApplicationName('Gmail API PHP Quickstart');
        $client->setScopes(Gmail::MAIL_GOOGLE_COM);
        $client->setAuthConfig(storage_path('credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setRedirectUri(route('gmail.callback'));

        $tokenPath = storage_path('token.json');

        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);

            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
                }
            }
        }

        return $client;
    }
}
