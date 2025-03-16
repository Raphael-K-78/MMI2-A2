<?php
namespace App\Controller;

use App\Entity\Images;
use App\Form\ImagesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;//mettre l'image dans la base de donnée

class NewImageController extends AbstractController
{
    #[Route('/AddImage', name: 'image_galerie')]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $image = new Images();

        $form = $this->createForm(ImagesType::class, $image);

        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($image);
            $entityManager->flush();
            $this->addFlash('réussi', 'L\'image a été ajoutée avec succès !');
            return $this->redirectToRoute('image_galerie');
        }

        return $this->render('image.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    public function upload(ManagerRegistry $doctrine, Request $request): Response
    {
        $image = new Images();
        $form = $this->createForm(ImagesType::class, $image);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get('fichier')->getData();
            
            if ($fichier) {
                $nomFichier = uniqid() . '.' . $fichier->guessExtension();
                try {
                    // Déplacer le fichier dans le répertoire des images
                    $fichier->move(
                        $this->getParameter('images_directory'), 
                        $nomFichier
                    );
                    
                    $image->setLink($nomFichier);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload du fichier.');
                }
            }

            // Persister l'entité Images dans la base de données
            $entityManager = $doctrine->getManager();
            $entityManager->persist($image);
            $entityManager->flush();

            // Ajouter un message de succès
            $this->addFlash('réussi', 'L\'image a été ajoutée avec succès !');
            
            // Rediriger vers la galerie d'images
            return $this->redirectToRoute('image_galerie');
        }

        // Rendu du formulaire dans la vue
        return $this->render('image.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
