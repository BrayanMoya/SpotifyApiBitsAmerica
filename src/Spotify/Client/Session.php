<?php

namespace Spotify\Client;

class Session
{
    protected $accessToken = '';
    protected $clientId = '';
    protected $clientSecret = '';
    protected $expirationTime = 0;
    protected $scope = '';
    protected $request = null;

    /**
     * Constructor
     * Set up client credentials.
     *
     * @param string $clientId The client ID.
     * @param string $clientSecret Optional. The client secret.
     * @param Request $request Optional. The Request object to use.
     */
    public function __construct($clientId, $clientSecret = '', $request = null)
    {
        $this->setClientId($clientId);
        $this->setClientSecret($clientSecret);

        $this->request = $request ?: new Request();
    }

    /**
     * Get the access token.
     *
     * @return string The access token.
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Get the client ID.
     *
     * @return string The client ID.
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get the client secret.
     *
     * @return string The client secret.
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Get the access token expiration time.
     *
     * @return int A Unix timestamp indicating the token expiration time.
     */
    public function getTokenExpiration()
    {
        return $this->expirationTime;
    }

    /**
     * Get the scope for the current access token
     *
     * @return array The scope for the current access token
     */
    public function getScope()
    {
        return explode(' ', $this->scope);
    }

    /**
     * Request an access token using the Client Credentials Flow.
     *
     * @return bool True when an access token was successfully granted, false otherwise.
     */
    public function requestCredentialsToken(): bool
    {
        $payload = base64_encode($this->getClientId() . ':' . $this->getClientSecret());

        $parameters = [
            'grant_type' => 'client_credentials',
        ];

        $headers = [
            'Authorization' => 'Basic ' . $payload,
        ];

        /** get access token */
        $response = $this->request->account('POST', '/api/token', $parameters, $headers);
        $response = $response['body'];

        if (isset($response->access_token)) {
            $this->accessToken = $response->access_token;
            $this->expirationTime = time() + $response->expires_in;
            $this->scope = $response->scope ?? $this->scope;

            return true;
        }

        return false;
    }

    /**
     * Set the client ID.
     *
     * @param string $clientId The client ID.
     *
     * @return void
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * Set the client secret.
     *
     * @param string $clientSecret The client secret.
     *
     * @return void
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }
}
