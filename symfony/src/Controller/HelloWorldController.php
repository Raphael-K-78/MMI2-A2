<?php
/*
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HelloWorldController extends AbstractController
{
    #[Route('/hello/world', name: 'app_hello_world')]
    public function index(): Response
    {
        return $this->render('hello_world/index.html.twig', [
            'controller_name' => 'HelloWorldController',
        ]);
    }
} */
?>
<?php
 
namespace App\Controller; //définir le code comme controleur dans l'app symfony

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;// importer le module pour gérer les vues les redirection
use Symfony\Component\HttpFoundation\Response; //retourner une réponse http
use Symfony\Component\Routing\Attribute\Route; // outil de défnition de l'url

final class HelloWorldController extends AbstractController //cr"ation du controleur
{
    //#[Route('/hello_world', name: 'app_hello_world')] //la route et le nom intern a symfony
    public function index(String $name): Response //methode appelé quand quelqu'un visite la page
    {
      //return new Response('Hello World !');// réponse http avec le contenue.
        return $this->render('hello.html.twig',['text'=>'Hello World '.$name." !"]);
    }
}
?> 