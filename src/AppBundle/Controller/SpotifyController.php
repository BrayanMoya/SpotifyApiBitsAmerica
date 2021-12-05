<?php

namespace AppBundle\Controller;

use Exception;
use Spotify\Client\ApiResources;
use Spotify\Client\Session;
use Spotify\ParsedResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpotifyController extends Controller
{

    /**
     * @throws Exception
     */
    public function releasesAction(Request $request)
    {
        $releasesParameters = $request->query->get('releasesParameters', '');
        $spotifyParameters = $this->getParameter('spotify');
        $clientId = $spotifyParameters['clientId'];
        $clientSecret = $spotifyParameters['clientSecret'];

        $session = new Session($clientId, $clientSecret);

        $session->requestCredentialsToken();
        $accessToken = $session->getAccessToken();

        if (!isset($accessToken)) {
            print_r('Invalid access token');
            exit;
        }

        $api = new ApiResources();
        $api->setAccessToken($accessToken);


        $releases = json_decode(json_encode($api->getNewReleases([], $releasesParameters)), true);
        $parsedResponse = new ParsedResponse($releases);
        $parsedResponse->unsetMarkets();

        $newReleases = $parsedResponse->getResponse();
        $albums = $newReleases['albums'];
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
}
