<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ContactoController extends AbstractController
{
  
     #[Route('/contacto/{codigo}', name: 'ficha_contacto')]
 
   public function ficha($codigo): Response
   {
       $resultado = $this->contactos[$codigo] ?? null;


       if($resultado){
           return $this->render('contacto/ficha_contacto.html.twig', [
               'codigo' => $resultado
           ]);
       }else{
           return new Response("<html><body>Contacto $codigo no encontrado</body></html>");
       }
   }
}




