<?php
namespace App\Services;


use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class AuthService{

    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $authUrl;
    private $tokenUrl;
    protected $config;

    public function __construct()
    {
        $this->config = config('quickbooks');
    }

    public function generateAuthorizationUrl()
    {
        //generate a unique state parameter
        $state = Str::random(40);
        // Redis::set('quickbooks_state_'.$state,true,'EX',300);
        // Build the authorization URL
        $authorizationUrl = $this->config[''] . '?' . http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'com.intuit.quickbooks.accounting', // Adjust scope as needed
            'state' => $state,
        ]);
    }

}
