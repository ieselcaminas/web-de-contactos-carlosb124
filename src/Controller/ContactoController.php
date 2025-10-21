<?php


namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Contacto;
use App\Entity\Provincia;
use Symfony\Bridge\Doctrine\ManagerRegistry as DoctrineManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ContactoFormType as ContactoType;
class ContactoController extends AbstractController

{
    

#[Route('/contacto/{codigo}', name: 'ficha_contacto')]


    private $contactos = [
        '001' => ['nombre' => 'Juan', 'telefono' => '123456789', 'email' => 'juan@example.com'],
        '002' => ['nombre' => 'Ana', 'telefono' => '987654321', 'email' => 'ana@example.com'],
    ];


    #[Route('/contacto/insertar', name: 'insertar_contacto')]
     public function insertar (ManagerRegistry $doctrine): Response
   {
       $entityManager = $doctrine->getManager();

      foreach ($this->contactos as $c) {
           $contacto = new Contacto();
           $contacto->setNombre($c['nombre']);
           $contacto->setTelefono($c['telefono']);
           $contacto->setEmail($c['email']);

           $entityManager->persist($contacto);
       }


    try {
       $entityManager->flush();

       return new Response('Contactos insertados con id '.$contacto->getId());
    }catch (\Exception $e) {
       return new Response("Error: ");
    }
   }
 
   public function ficha(ManagerRegistry $doctrine, $codigo): Response
   {

        $repositorio = $doctrine->getRepository(Contacto::class);
        $resultado = $this->contactos[$codigo] ?? null;


       if($resultado){
          return $this->render('contacto/ficha_contacto.html.twig', [
         'contacto' => $resultado
         ]);




       }else{
           return new Response("<html><body>Contacto $codigo no encontrado</body></html>");
       }
   }
    #[Route('/contacto/buscar/{texto}', name: 'buscar_contacto')]
    public function buscar(ManagerRegistry $doctrine, $texto): Response
{
    $repositorio = $doctrine->getRepository(Contacto::class);
    $contactos = $repositorio->findBy(['nombre' => $texto]);

    return $this->render('contacto/buscar_contacto.html.twig', [
        'contactos' => $contactos,
        'texto' => $texto
    ]);
}
    

#[Route('/contacto/update/{id}/{nombre}', name: 'modificar_contacto')]

public function update(ManagerRegistry $doctrine, $id, $nombre): Response{
    $entityManager = $doctrine->getManager();
    $repositorio = $doctrine->getRepository(Contacto::class);
    $contacto = $repositorio->find($id);

    if ($contacto) {
        $contacto->setNombre($nombre);
        
        try {
            $entityManager->flush();
            return $this->render('ficha_contacto.html.twig', [
                'contacto' => $contacto
            ]);
        } catch (\Exception $e) {
            return new Response("Error al actualizar el contacto: " . $e->getMessage());
        }
    } else {
        return $this->render('ficha_contacto.html.twig', [
            'contacto' => null
        ]);



    }
}
#[Route('/contacto/delete/{id}', name: 'eliminar_contacto')]


public function delete(ManagerRegistry $doctrine, $id): Response{
    $entityManager = $doctrine->getManager();
    $repositorio = $doctrine->getRepository(Contacto::class);
    $contacto = $repositorio->find($id);

    if ($contacto) {
        try {
            $entityManager->remove($contacto);
            $entityManager->flush();
            return new Response("Contacto con id $id eliminado correctamente.");
        } catch (\Exception $e) {
            return new Response("Error al eliminar el contacto: " . $e->getMessage());
        }
    } else {
        return new Response("Contacto con id $id no encontrado.");
    }

}
    #[Route('/contacto/insertarConProvincia', name: 'insertar_con_provincia')]
    public function insertarConProvincia(ManagerRegistry $doctrine): Response{
        $entityManager = $doctrine->getManager();

        $provincia = new Provincia();
        $provincia->setNombre('Alicante');

        $contacto = new Contacto();
        $contacto->setNombre("Inserción de prueba con provincia");
        $contacto->setTelefono("123456789");
        $contacto->setEmail("INSERCION.DE.PRUEBA.PROVINCIA@contacto.es");
        $contacto->setProvincia($provincia);

        $entityManager->persist($provincia);
        $entityManager->persist($contacto);

        $entityManager->flush();
        return $this->render('ficha_contacto.html.twig', [
            'contacto' => $contacto
        ]);
    }

#[Route('/contacto/insertarSinProvincia', name: 'insertar_sin_provincia')]
public function insertarSinProvincia(ManagerRegistry $doctrine): Response{
    $entityManager = $doctrine->getManager();
    $repositorio = $doctrine->getRepository(Provincia::class);

    $provincia = $repositorio->findOneBy(['nombre' => 'Alicante']);

    $contacto = new Contacto();

    $contacto->setNombre("Inserción de prueba sin provincia");
    $contacto->setTelefono("987654321");
    $contacto->setEmail("INSERCION.DE.PRUEBA.SIN.PROVINCIA@contacto.es");
    $contacto->setProvincia($provincia);

    $entityManager->persist($contacto);

    $entityManager->flush();
    return $this->render('ficha_contacto.html.twig', [
        'contacto' => $contacto
    ]);
}

#[Route('/contacto/nuevo', name: 'nuevo')]

public function nuevo(ManagerRegistry $doctrine, Request $request) {

    $this->getUser();  
    
    $contacto = new Contacto();

        $formulario = $this->createForm(ContactoType::class, $contacto);

        $formulario->handleRequest($request);



        if ($formulario->isSubmitted() && $formulario->isValid()) {

            $contacto = $formulario->getData();

            

            $entityManager = $doctrine->getManager();

            $entityManager->persist($contacto);

            $entityManager->flush();

            return $this->redirectToRoute('ficha_contacto', ["codigo" => $contacto->getId()]);

        }

        return $this->render('nuevo.html.twig', array(

            'formulario' => $formulario->createView()

        ));

    }
    #[Route('/contacto/editar/{codigo}', name: 'editar', requirements:["codigo"=>"\d+"])]

public function editar(ManagerRegistry $doctrine, Request $request, int $codigo) {

    $repositorio = $doctrine->getRepository(Contacto::class);

    //En este caso, los datos los obtenemos del repositorio de contactos

    $contacto = $repositorio->find($codigo);

    if ($contacto){

        $formulario = $this->createForm(ContactoType::class, $contacto);



        $formulario->handleRequest($request);



        if ($formulario->isSubmitted() && $formulario->isValid()) {

            //Esta parte es igual que en la ruta para insertar

            $contacto = $formulario->getData();

            $entityManager = $doctrine->getManager();

            $entityManager->persist($contacto);

            $entityManager->flush();

            return $this->redirectToRoute('ficha_contacto', ["codigo" => $contacto->getId()]);

        }

        return $this->render('nuevo.html.twig', array(

            'formulario' => $formulario->createView()

        ));

    }else{

        return $this->render('ficha_contacto.html.twig', [

            'contacto' => NULL

        ]);

    }

}


}