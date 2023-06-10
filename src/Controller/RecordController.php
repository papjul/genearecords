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

#[Route('/{_locale<%app.supported_locales%>}/record')]
class RecordController extends AbstractController
{
    #[Route('/view/{id<[A-Z0-9]{26}>}', name: 'record_view', methods: ["GET", "POST"])]
    public function index(Record $record): Response
    {
        return $this->render('record/view.html.twig', [
            'record' => $record
        ]);
    }
}
