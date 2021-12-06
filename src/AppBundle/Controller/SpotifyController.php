<?php

namespace AppBundle\Controller;

use Exception;
use Spotify;
use Spotify\Client\ApiException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SpotifyController extends Controller
{

    /**
     * @throws Exception
     */
    public function releasesAction(Request $request)
    {
        $releasesParameters = $request->query->get('releasesParameters', '');
        $spotifyParameters = $this->getParameter('spotify');

        try {
            $releases = Spotify\Response::getReleasesResponse($releasesParameters, $spotifyParameters);
        } catch (ApiException $e) {
            return $e->getReason();
        }

        $albums = $releases['albums'];
        $items = $albums['items'];
        $limit = $albums['limit'];
        $offset = $albums['offset'];
        $total = $albums['total'];
        $PreviousReleasesParameters = explode('?', $albums['previous'])[1] ?? null;
        $NextReleasesParameters = explode('?', $albums['next'])[1] ?? null;

        $urlToPreviousReleases = $PreviousReleasesParameters ? $this->generateUrl('last_releases',
            [
                '_locale' => $request->getLocale(),
                'releasesParameters' => $PreviousReleasesParameters
            ]) : '';

        $urlToNextReleases = $NextReleasesParameters ? $this->generateUrl('last_releases', [
            '_locale' => $request->getLocale(),
            'releasesParameters' => $NextReleasesParameters

        ]) : '';

        return $this->render('spotify/releases.html.twig', [
            'items' => $items,
            'limit' => $limit,
            'offset' => $offset,
            'urlToPreviousReleases' => $urlToPreviousReleases,
            'urlToNextReleases' => $urlToNextReleases,
            'total' => $total,
        ]);
    }

    public function artistAction(Request $request)
    {
        $spotifyParameters = $this->getParameter('spotify');
        $artistId = $request->query->get('id', '');

        try {
            $artist = Spotify\Response::getArtistResponse($spotifyParameters, $artistId);
            $artistTracks = Spotify\Response::getArtistTracksResponse($spotifyParameters, $artistId);
        } catch (ApiException $e) {
            return $e->getReason();
        }

        $artistImage = $artist['images'][1]['url'];
        $artistSpotifyPage = $artist['external_urls']['spotify'];
        $artistName = $artist['name'];
        $tracks = $artistTracks['tracks'];
        $urlToReleases = $this->generateUrl('last_releases', [
            '_locale' => $request->getLocale()
        ]);

        return $this->render('spotify/artist.html.twig', [
            'artistImage' => $artistImage,
            'artistSpotifyPage' => $artistSpotifyPage,
            'artistName' => $artistName,
            'tracks' => $tracks,
            'urlToReleases' => $urlToReleases
        ]);
    }

}
