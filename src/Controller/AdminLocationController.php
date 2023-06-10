<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale<%app.supported_locales%>}/admin/location')]
class AdminLocationController extends AbstractController
{
    #[Route('', name: 'admin_locations')]
    public function index(): Response
    {
        return $this->render('admin/locations.html.twig');
    }

    #[Route('/add-missings', name: 'admin_location_add_missings')]
    public function addMissings(EntityManagerInterface $entityManager): Response
    {
        $locations = $entityManager->getRepository(Event::class)->getUniqueLocations();
        foreach ($locations as $location) {
            $existingLocation = $entityManager->getRepository(Location::class)->findOneBy([
                'place' => $location['place'],
                'province' => $location['province'],
                'country' => $location['country']
            ]);

            if ($existingLocation === null) {
                $locationEntry = new Location();
                $locationEntry->setPlace($location['place']);
                $locationEntry->setProvince($location['province']);
                $locationEntry->setCountry($location['country']);
                // Why not:
                //$entityManager->getRepository(Event::class)->setStatistics($locationEntry);
                $entityManager->persist($locationEntry);
            }
        }
        $entityManager->flush();
        $this->addFlash('success', 'Locations updated.');

        return $this->render('admin/location-add-missings.html.twig');
    }

    #[Route('/regenerate-statistics', name: 'admin_location_regenerate_statistics')]
    public function regenerateStatistics(EntityManagerInterface $entityManager): Response
    {
        $locations = $entityManager->getRepository(Location::class)->findAll();
        foreach ($locations as $location) {
            $entityManager->getRepository(Event::class)->setStatistics($location);
        }
        $entityManager->flush();
        $this->addFlash('success', 'Statistics updated.');

        return $this->render('admin/location-regenerate-statistics.html.twig');
    }
}
