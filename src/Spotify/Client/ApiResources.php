<?php

namespace Spotify\Client;

class ApiResources
{
    protected $accessToken = '';
    protected $lastResponse = [];
    protected $options = [
        'auto_refresh' => false,
        'auto_retry' => false,
        'return_assoc' => false,
    ];
    protected $request = null;
    protected $session = null;

    /**
     * Constructor
     * Set options and class instances to use.
     *
     * @param array|object $options Optional. Options to set.
     * @param Session $session Optional. The Session object to use.
     * @param Request $request Optional. The Request object to use.
     */
    public function __construct($options = [], $session = null, $request = null)
    {
        $this->setOptions($options);
        $this->setSession($session);

        $this->request = $request ?: new Request();
    }

    /**
     * Set the access token to use.
     *
     * @param string $accessToken The access token.
     *
     * @return void
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Set options
     *
     * @param array|object $options Options to set.
     *
     * @return void
     */
    public function setOptions($options)
    {
        $this->options = array_merge($this->options, (array)$options);
    }

    /**
     * Set the Session object to use.
     *
     * @param Session $session The Session object.
     *
     * @return void
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * Get the Request object in use.
     *
     * @return Request The Request object in use.
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Add authorization headers.
     *
     * @param $headers array. Optional. Additional headers to merge with the authorization headers.
     *
     * @return array Authorization headers, optionally merged with the passed ones.
     */
    protected function authHeaders($headers = [])
    {
        $accessToken = $this->session ? $this->session->getAccessToken() : $this->accessToken;

        if ($accessToken) {
            $headers = array_merge($headers, [
                'Authorization' => 'Bearer ' . $accessToken,
            ]);
        }

        return $headers;
    }

    /**
     * Send a request to the Spotify API, automatically refreshing the access token as needed.
     *
     * @param string $method The HTTP method to use.
     * @param string $uri The URI to request.
     * @param array $parameters Optional. Query string parameters or HTTP body, depending on $method.
     * @param array $headers Optional. HTTP headers.
     *
     * @return array Response data.
     * - array|object body The response body. Type is controlled by the `return_assoc` option.
     * - array headers Response headers.
     * - int status HTTP status code.
     * - string url The requested URL.
     * @throws ApiAuthException
     *
     * @throws ApiException
     */
    protected function sendRequest($method, $uri, $parameters = [], $headers = [])
    {
        $this->request->setOptions([
            'return_assoc' => $this->options['return_assoc'],
        ]);

        try {
            $headers = $this->authHeaders($headers);

            return $this->request->api($method, $uri, $parameters, $headers);
        } catch (ApiException $e) {
            if ($this->options['auto_refresh'] && $e->hasExpiredToken()) {
                $result = $this->session->refreshAccessToken();

                if (!$result) {
                    throw new ApiException('Could not refresh access token.');
                }

                return $this->sendRequest($method, $uri, $parameters, $headers);
            }

            if ($this->options['auto_retry'] && $e->isRateLimited()) {
                $lastResponse = $this->request->getLastResponse();
                $retryAfter = (int)$lastResponse['headers']['retry-after'];

                sleep($retryAfter);

                return $this->sendRequest($method, $uri, $parameters, $headers);
            }

            throw $e;
        }
    }

    /**
     * Convert URIs to Spotify object IDs.
     *
     * @param array|string $uriIds URI(s) to convert.
     * @param string $type Spotify object type.
     *
     * @return array|string ID(s).
     */
    protected function uriToId($uriIds, $type)
    {
        $type = 'spotify:' . $type . ':';

        $uriIds = array_map(function ($id) use ($type) {
            return str_replace($type, '', $id);
        }, (array)$uriIds);

        return count($uriIds) == 1 ? $uriIds[0] : $uriIds;
    }

    /**
     * Get an artist.
     * https://developer.spotify.com/documentation/web-api/reference/#endpoint-get-an-artist
     *
     * @param string $artistId ID or URI of the artist.
     *
     * @return array|object The requested artist. Type is controlled by the `return_assoc` option.
     */
    public function getArtist($artistId)
    {
        $artistId = $this->uriToId($artistId, 'artist');
        $uri = "/v1/artists/$artistId";

        $this->lastResponse = $this->sendRequest('GET', $uri);

        return $this->lastResponse['body'];
    }

    /**
     * Get an artist's top tracks in a country.
     * https://developer.spotify.com/documentation/web-api/reference/#endpoint-get-an-artists-top-tracks
     *
     * @param string $artistId ID or URI of the artist.
     * @param array|object $options Options for the tracks.
     * - string $country Required. An ISO 3166-1 alpha-2 country code specifying the country to get the top tracks for.
     *
     * @return array|object The artist's top tracks. Type is controlled by the `return_assoc` option.
     */
    public function getArtistTopTracks($artistId, $options)
    {
        $artistId = $this->uriToId($artistId, 'artist');
        $uri = "/v1/artists/$artistId/top-tracks";

        $this->lastResponse = $this->sendRequest('GET', $uri, $options);

        return $this->lastResponse['body'];
    }

    /**
     * Get new releases.
     * https://developer.spotify.com/documentation/web-api/reference/#endpoint-get-new-releases
     *
     * @param array|object $options Optional. Options for the items.
     * - string country Optional. An ISO 3166-1 alpha-2 country code. Show items relevant to this country.
     * - int limit Optional. Limit the number of items.
     * - int offset Optional. Number of items to skip.
     *
     * @return array|object The new releases. Type is controlled by the `return_assoc` option.
     */
    public function getNewReleases($options = [], $parameters = '')
    {
        $parameters = $parameters !== '' ? "?$parameters" : '';
        $uri = "/v1/browse/new-releases$parameters";

        $this->lastResponse = $this->sendRequest('GET', $uri, $options);

        return $this->lastResponse['body'];
    }
}
