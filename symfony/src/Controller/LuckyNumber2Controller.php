<?php
 
namespace App\Controller; //définir le code comme controleur dans l'app symfony

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;// importer le module pour gérer les vues les redirection
use Symfony\Component\HttpFoundation\Response; //retourner une réponse http
use Symfony\Component\Routing\Attribute\Route; // outil de défnition de l'url

final class LuckyNumber2Controller extends AbstractController //cr"ation du controleur
{
    #[Route('/number2', name: 'app_lucky_number2')] //la route et le nom intern a symfony
    public function index(): Response //methode appelé quand quelqu'un visite la page
    {
        $number = rand(0,100);
      //return new Response('Hello World !');// réponse http avec le contenue.
        return $this->render('number.html.twig',['number'=> $number]);
    }
}
?> 