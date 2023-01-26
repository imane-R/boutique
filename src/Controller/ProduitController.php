<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/produits', name: 'app_produits')]
    public function showAll(ProduitRepository $repo, CategorieRepository $repoCat): Response
    {
        $produits = $repo->findAll();

        $categories = $repoCat->findAll();
        return $this->render('produit/allProducts.html.twig', [
            'produits' => $produits,
            'categories' => $categories

        ]);
    }
    #[Route('/produits/{slug}', name: 'app_produits_categorie')]

    public function showByCategorie($slug, CategorieRepository $repo)
    {
        $categorie = $repo->findOneBy(['slug' => $slug]);
        $categories = $repo->findAll();

        return $this->render('produit/allProducts.html.twig', [
            'categories' => $categories,
            'produits' => $categorie->getProduits()
        ]);
    }
    #[Route('/produit{id<\d+>}', name: 'app_produit_show')]
    public function show($id, ProduitRepository $repo)
    {
        $produit = $repo->find($id);
        return $this->render('produit/show.html.twig', [
            'produit' => $produit
        ]);
    }
}
