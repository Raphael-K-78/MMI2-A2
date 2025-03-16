<?php
 
namespace App\Controller; //définir le code comme controleur dans l'app symfony

use App\Entity\Images;//poo Image
use Doctrine\Persistence\ManagerRegistry;//mettre l'image dans la base de donnée

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;// importer le module pour gérer les vues les redirection
use Symfony\Component\HttpFoundation\Response; //retourner une réponse http
use Symfony\Component\Routing\Attribute\Route; // outil de défnition de l'url

final class GalerieAddController extends AbstractController //cr"ation du controleur
{
    #[Route('/GalerieAdd', name: 'app_add_controller')] //la route et le nom intern a symfony
    public function addImage(ManagerRegistry $doctrine) : Response {
        
        $image = new Images();
        $image->setTitre('The Canard');
        $image->setAuteur('King Coin');
        $image->setLink('/src/img/Canard1.jpeg');
        
        $em = $doctrine ->getManager();
        $em->persist($image);
        $em->flush();

        return $this->render('AddGalerie.html.twig',['image'=>$image]);//si on veut afficher ce qu'on voit
        // return $this->redirectToRoute('app_galerie'); //si on veut rediriger vers la galerie
    }
}
?> 