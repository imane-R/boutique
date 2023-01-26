<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProduitRepository $repo): Response
    {
        $produits = $repo->findBy([], ["id" => "DESC"], 5);
        return $this->render('home/index.html.twig', [
            'produits' => $produits,
        ]);
    }

  
}
