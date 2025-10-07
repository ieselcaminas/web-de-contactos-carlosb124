<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PageController extends AbstractController
{
   private $contactos = [
       1 => ["nombre" => "Juan Pérez", "telefono" => "524142432", "email" => "juanp@ieselcaminas.org"],
       2 => ["nombre" => "Ana López", "telefono" => "58958448", "email" => "anita@ieselcaminas.org"],
       5 => ["nombre" => "Mario Montero", "telefono" => "5326824", "email" => "mario.mont@ieselcaminas.org"],
       7 => ["nombre" => "Laura Martínez", "telefono" => "42898966", "email" => "lm2000@ieselcaminas.org"],
       9 => ["nombre" => "Nora Jover", "telefono" => "54565859", "email" => "norajover@ieselcaminas.org"]
   ];


   #[Route('/page', name: 'app_page')]
   public function index(): JsonResponse
   {
       return $this->json([
           'message' => 'Welcome to your new controller!',
           'path' => 'src/Controller/PageController.php',
       ]);
   }


   #[Route('/', name: 'inicio')]
   public function inicio(): Response
   {
       return $this->render('inicio.html.twig');
   }


   #[Route('/contacto/{codigo}', name: 'ficha_contacto')]
   public function ficha($codigo): Response
   {
       //$resultado = $this->contactos[$codigo] ?? null;


       //if ($resultado) {
         //  $html = "<ul>";
           //$html .= "<li>" . htmlspecialchars($codigo) . "</li>";
           //$html .= "<li>" . htmlspecialchars($resultado['nombre']) . "</li>";
           //$html .= "<li>" . htmlspecialchars($resultado['telefono']) . "</li>";
           //$html .= "<li>" . htmlspecialchars($resultado['email']) . "</li>";
           //$html .= "</ul>"; // Fix: Use .= to append closing tag instead of overwriting


           //return new Response("<html><body>$html</body></html>"); // Added closing tags
       //}


       $contacto = ($this->contactos[$codigo] ?? null);


       if ($contacto){
           return $this->render('ficha_contacto.html.twig', ["contacto" => $contacto]);
       }


       return new Response("<html><body>Contacto $codigo no encontrado</body></html>"); // Added closing tags
   }
}
