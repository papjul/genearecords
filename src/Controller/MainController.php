<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Record;
use App\Form\SearchFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/')]
    public function indexNoLocale(Request $request): Response
    {
        $redirectLocale = 'en'; // Default language
        $userLocale = $request->getLocale();
        dump($userLocale);
        if (in_array($userLocale, explode('|', $this->getParameter('app.supported_locales')))) {
            $redirectLocale = $userLocale;
        }
        return $this->redirectToRoute('homepage', ['_locale' => $redirectLocale]);
    }

    #[Route('/{_locale<%app.supported_locales%>}', name: 'homepage', methods: ["GET", "POST"])]
    public function index(Request $request, FormFactoryInterface $formFactory): Response
    {
        $form = $formFactory->createBuilder(SearchFormType::class)
            ->setAction($this->generateUrl('search_results'))
            ->getForm();
        $form->handleRequest($request);
        return $this->render('index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/contribute', name: 'contribute', methods: ["GET"])]
    public function contribute(): Response
    {
        return $this->render('contribute.html.twig');
    }
}
