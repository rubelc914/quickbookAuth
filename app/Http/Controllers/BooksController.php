<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class BooksController extends Controller
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $authUrl;
    private $tokenUrl;

    public function __construct()
    {
        $this->clientId = 'ABqF87hzP4vU7TRfTeYaDHqYNsnH6gHSkAcZBTym43cgFfPbP0'; 
        $this->clientSecret = 'dTEXTmfP7VmxGHBGhLwXxsDmVnIX1fMRJEb75gp9'; 
        $this->redirectUri = 'http://localhost:8000/quickbooks/callback'; 

        // Discovery document URLs
        $this->authUrl = 'https://appcenter.intuit.com/connect/oauth2';
        $this->tokenUrl = 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer';
    }

    public function connect()
    {
        // Generate a unique state parameter
        $state = Str::random(40);
        Session::put('quickbooks_state', $state);

        // Build the authorization URL
        $authorizationUrl = $this->authUrl . '?' . http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'com.intuit.quickbooks.accounting', 
            'state' => $state,
        ]);

        return redirect()->away($authorizationUrl);
    }

    public function callback(Request $request)
    {
        $state = $request->input('code');
        $storedState = Session::get('quickbooks_state');

        // if (!$state || $state !== $storedState) {
        //     return redirect()->route('home')->with('error', 'Invalid state parameter');
        // }

       

        // Obtain authorization code
        $code = $request->input('code');

        if (!$code) {
            return redirect()->route('home')->with('error', 'Authorization code not found');
        }

        // Exchange authorization code for access token
        try {
            $client = new Client();
            $response = $client->post($this->tokenUrl, [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);
            dd($body);
            // Handle response and save tokens securely
            $accessToken = $body['access_token'];
            $refreshToken = $body['refresh_token'];

            return redirect()->route('home')->with('success', 'Connected to QuickBooks successfully');
        } catch (\Exception $e) {
            Log::error('Error exchanging authorization code for access token: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Failed to fetch tokens from QuickBooks');
        }
    }
}
