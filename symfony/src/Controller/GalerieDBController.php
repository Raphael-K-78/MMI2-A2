<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Images;//poo Image
use Doctrine\Persistence\ManagerRegistry;//mettre l'image dans la base de donnée

final class GalerieDBController extends AbstractController
{
    #[Route('/Galerie/{page}', name: 'app_galerie2')]
    public function list(ManagerRegistry $doctrine,int $page = 1): Response
    {
        $img_per_page = 3;

        $em= $doctrine->getManager();
        $repo=$em->getRepository(Images::class);

        $images = $repo->findBy([],null);
        $total_images = count($images);
        $total_pages = ceil($total_images / $img_per_page);

        // S'assurer que la page est dans les limites
        $page = max(1, min($page, $total_pages));
        // Calculer les images de la page actuelle
        $current_images = array_slice($images, ($page - 1) * $img_per_page, $img_per_page);

        // Calculer les pages précédente et suivante
        $page_before = $page > 1 ? $page - 1 : null;
        $page_after = $page < $total_pages ? $page + 1 : null;

        return $this->render('gallery2.html.twig', [
            'images' => $current_images,
            'current_page' => $page,
            'page_before' => $page_before,
            'page_after' => $page_after,
            'total_pages' => $total_pages
        ]);
    }
}
