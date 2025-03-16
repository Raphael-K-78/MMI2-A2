<?php
namespace App\Controller;

use App\Entity\Images;
use App\Form\ImagesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class ImageController extends AbstractController
{
    #[Route('/AddImage', name: 'upload_image')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            
            return $this->redirectToRoute('app_galerie2');  
        }
        $image = new Images();
        $form = $this->createForm(ImagesType::class, $image);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->get('fichier')->getData();
            
            if ($fichier) {
                $nomFichier = uniqid() . '.' . $fichier->guessExtension();
                try {
                    $fichier->move(
                        $this->getParameter('images_directory'), 
                        $nomFichier
                    );
                    
                    $image->setLink("/img/" . $nomFichier);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload du fichier.');
                }
            }

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
}


?>