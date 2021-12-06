<?php

namespace Spotify;

use Spotify\Client\ApiException;
use Spotify\Client\ApiResources;
use Spotify\Client\Session;

class Response
{
    /** @var ApiResources $apiResource */
    private static $apiResource;

    /**
     * @param $spotifyParameters
     * @return string
     * @throws ApiException
     */
    protected function getAccessToken($spotifyParameters): string
    {
        $clientId = $spotifyParameters['clientId'];
        $clientSecret = $spotifyParameters['clientSecret'];

        $session = new Session($clientId, $clientSecret);

        $session->requestCredentialsToken();
        $accessToken = $session->getAccessToken();

        if (!isset($accessToken)) {
            throw new ApiException('Invalid access token');
        }

        return $accessToken;
    }

    protected function getApiResources($spotifyParameters): ApiResources
    {
        $accessToken = self::getAccessToken($spotifyParameters);
        $api = new ApiResources();
        $api->setAccessToken($accessToken);

        self::$apiResource = $api;

        return $api;
    }
    /**
     * @throws ApiException
     */
    public static function getReleasesResponse($releasesParameters, $spotifyParameters): array
    {
        $apiResources = self::$apiResource ?? self::getApiResources($spotifyParameters);
        $releases = json_decode(json_encode($apiResources->getNewReleases([], $releasesParameters)), true);
        $parsedResponse = new ParsedResponse($releases);
        $parsedResponse->unsetMarkets();

        return $parsedResponse->getResponse();
    }

    public static function getArtistResponse($spotifyParameters, $artistId)
    {
        $apiResources = self::$apiResource ?? self::getApiResources($spotifyParameters);
        return json_decode(json_encode($apiResources->getArtist($artistId)), true);
    }

    public static function getArtistTracksResponse($spotifyParameters, $artistId)
    {
        $apiResources = self::$apiResource ?? self::getApiResources($spotifyParameters);
        $artistTracks = json_decode(json_encode($apiResources->getArtistTopTracks($artistId, ['market' => 'US'])), true);
        $parsedResponse = new ParsedResponse($artistTracks);
        $parsedResponse->validateArtist($artistId);

        return $parsedResponse->getResponse();
    }

}
