<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale<%app.supported_locales%>}/admin')]
class AdminController extends AbstractController
{
    #[Route('', name: 'admin_index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
