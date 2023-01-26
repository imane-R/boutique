<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Form\ProduitType;
use App\Form\CategorieType;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin', name: 'admin_')] // cette route est commune à atoutes celle qui se trouvent dans ce controller 
class AdminController extends AbstractController
{

    // --------------------------------Admin Categories ----------------------------------------------------

    #[Route('/categorie_add', name: 'app_categorie_add')] // pour accéder à cette route via le navirateur il faut donc ajouter /admin devant 
    public function add(Request $request, CategorieRepository $repo, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        // $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //on recupere le saisie du champs du formulaire
            $categoriesForm = $form->get('nom')->getData();
            //on transforme la chaine de chara en tableau
            $tab = explode(',', $categoriesForm);
            foreach ($tab as $nom) {
                $categorie = new Categorie();
                $categorie->setNom($nom);
                $slug = $slugger->slug($nom); // on crée le slig à aprtir du nom de la categorie 
                $categorie->setSlug($slug); // on affect le $slug à la propriete "slug" de l'objet categorie 
                $repo->save($categorie); // persist
            }
            //on recuprer le manager de doctrine pour le flush et donc envoyer en bdd les categories persistées
            $manager = $doctrine->getManager();
            $manager->flush();

            return $this->redirectToRoute('admin_app_categories');
        }

        return $this->render("admin/categorie/form.html.twig", [
            'formCategorie' => $form->createView()
        ]);
    }

    #[Route('/categories', name: 'app_categories')]
    public function showAll(CategorieRepository $repo)
    {
        $categories = $repo->findAll();
        return $this->render("admin/categorie/showAllCategories.html.twig", [
            'categories' => $categories
        ]);
    }
    #[Route('/categorie_update_{slug}', name: 'app_categorie_update')]
    public function update($slug, Request $request, CategorieRepository $repo, SluggerInterface $slugger)
    {
        $categorie = $repo->findOneBy(['slug' => $slug]);
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug =  $slugger->slug($categorie->getNom());
            $categorie->setSlug($slug);
            $repo->save($categorie, 1);
            return $this->redirectToRoute('admin_app_categories');
        }
        return $this->render('admin/categorie/form.html.twig', [
            'formCategorie' => $form->createView()
        ]);
    }
    #[Route('/categorie_delete_{slug}', name: 'app_categorie_delete')]
    public function delete($slug, CategorieRepository $repo)
    {
        $categorie = $repo->findOneBy(['slug' => $slug]);
        $repo->remove($categorie, 1);
        return $this->redirectToRoute('admin_app_categories');
    }



    //------------------------------------------------- FIN Categorie ----------------------------------------------------------------

    // --------------------------------Debut Produits ----------------------------------------------------//

    #[Route('/produit_add', name: 'app_produit_add')]
    public function addProduct(Request $request, ProduitRepository $repo, SluggerInterface $slugger)
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photoForm')->getData();
            $fileName = $slugger->slug($produit->getTitre()) . uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($this->getParameter('photos_produit'), $fileName);
            } catch (FileException $e) {
                //gestion des erreurs d'upload
            }
            // dd($fileName);
            $produit->setPhoto($fileName);
            $repo->save($produit, 1);
            return $this->redirectToRoute('admin_app_produits_gestion');
        }
        return $this->render('admin/produit/formulaire.html.twig', [
            'formProduit' => $form->createView(),
        ]);
    }

    #[Route('/produits', name: 'app_produits_gestion')]
    public function gestionProduits(ProduitRepository $repo)
    {
        $produits = $repo->findAll();
        return $this->render("admin/produit/gestionProuduits.html.twig", [
            'produits' => $produits
        ]);
    }

    #[Route('/produit_details{id<\d+>}', name: 'app_produit_details')]
    public function detailsProduit($id, ProduitRepository $repo)
    {
        $produit = $repo->find($id);
        return $this->render('admin/produit/detailsProduit.html.twig', [
            'produit' => $produit
        ]);
    }
    #[Route('/produit_update{id<\d+>}', name: 'app_produit_update')]

    public function updateProduct($id, Request $request, ProduitRepository $repo, SluggerInterface $slugger)
    {
        $produit = $repo->find($id);
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photoForm')->getData();
            if ($file) {
                $fileName = $slugger->slug($produit->getTitre()) . uniqid() . '.' . $file->guessExtension();
                try {
                    $file->move($this->getParameter('photos_produit'), $fileName);
                } catch (FileException $e) {
                    //gestion des erreurs d'upload
                }
                $produit->setPhoto($fileName);
            }
            $repo->save($produit, 1);
            return $this->redirectToRoute('admin_app_produits_gestion');
        }
        return $this->render('admin/produit/formulaire.html.twig', [
            'formProduit' => $form->createView(),
        ]);
    }
    #[Route('/produit_delete{id<\d+>}', name: 'app_produit_delete')]
    public function deleteProduit($id, ProduitRepository $repo)
    {
        $produit = $repo->find($id);
        $repo->remove($produit, 1);

        return $this->redirectToRoute("admin_app_produits_gestion");
    }
}
