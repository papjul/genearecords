<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale<%app.supported_locales%>}/explore')]
class ExploreController extends AbstractController
{
    #[Route('', name: 'explore', methods: ["GET"])]
    public function explore(Request $request, EntityManagerInterface $entityManager): Response
    {
        // TODO: Make a breadcrumb
        $templateVariables = [];
        if ($request->query->has('province') && $request->query->has('country')) {
            $templateVariables['country'] = $request->query->get('country');
            $templateVariables['province'] = $request->query->get('province');
            $templateVariables['places'] = $entityManager->getRepository(Location::class)->getPlaces($request->query->get('country'), $request->query->get('province'));
            if (empty($templateVariables['places'])) {
                throw $this->createNotFoundException('This province in this country does not exist in our database.');
            }
        } elseif ($request->query->has('country')) {
            $templateVariables['country'] = $request->query->get('country');
            $templateVariables['provinces'] = $entityManager->getRepository(Location::class)->getProvinces($request->query->get('country'));
            if (empty($templateVariables['provinces'])) {
                throw $this->createNotFoundException('This country does not exist in our database.');
            }
        } else {
            $templateVariables['countries'] = $entityManager->getRepository(Location::class)->getCountries();
        }


        return $this->render('explore/index.html.twig', $templateVariables);
    }
}
