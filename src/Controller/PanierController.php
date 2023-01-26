<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier_add_{id<\d+>}', name: 'app_panier_add')]
    public function add($id, RequestStack $requestStack): Response
    {
        // on crée un objet session
        $session = $requestStack->getSession();

        //on rcée le panier dans la session si celui ci n'existe pas ou bien le recupere dans le cas si existe
        $panier = $session->get('panier', []);

        // on verfie si l'id existe eja , dans ce cas on increment sinon on le crée
        if (empty($panier[$id])) {
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }

        //on sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("app_panier");
    }
    #[Route('/panier', name: 'app_panier')]
    public function show(RequestStack $requestStack, ProduitRepository $repo)
    {
        $session = $requestStack->getSession();
        $panier = $session->get("panier", []);
        $dataPanier = [];
        $total = 0;
        foreach ($panier as $id => $quantite) {
            $produit = $repo->find($id);
            $dataPanier[] = [
                'produit' => $produit,
                "quantite" => $quantite
            ];
            $total += $produit->getPrix() * $quantite;
        }
        return $this->render('panier/index.html.twig', [
            'dataPanier' => $dataPanier,
            'total' => $total

        ]);
    }

    #[Route('/panier_delete_produit{id<\d+>}', name: 'app_panier_delete_product')]
    public function deleteProduit($id, RequestStack $requestStack ){
        $session = $requestStack->getSession();
        $panier = $session->get("panier", []);

        if(!empty($panier[$id])){
            unset($panier[$id]);

        }else{
            $this->addFlash('error'," le produit que vous essayer de retirer du panier n\'éxiste pas !!" );
            return $this->redirectToRoute('app_panier');
        }
        $session->set("panier", $panier);
        $this->addFlash('success'," Le produit à été retiré du panier!");
        return $this->redirectToRoute('app_panier');
    }

    

}
