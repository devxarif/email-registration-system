<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Gmail;

class GmailController extends Controller
{
    /**
     * Handle the incoming request to authorize Gmail API access.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authorize()
    {
        $client = new Client();
        $client->setApplicationName('Registration Application - Gmail API');
        $client->setScopes(Gmail::MAIL_GOOGLE_COM);
        $client->setAuthConfig(storage_path('credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setRedirectUri(route('gmail.callback'));

        $authUrl = $client->createAuthUrl();
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

        return redirect()->route('gmail.send')->with('success', 'Authorization successful!');
    }
}
