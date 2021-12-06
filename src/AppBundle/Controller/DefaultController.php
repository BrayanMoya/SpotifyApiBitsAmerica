<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request): ?Response
    {
        $redirectToReleases = $this->generateUrl('last_releases' , ['_locale' => $request->getLocale()]);

        return $this->render('default/index.html.twig', [
            'releasesUrl' => $redirectToReleases,
        ]);
    }

}
