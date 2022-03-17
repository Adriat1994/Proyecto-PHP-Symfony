<?php

namespace App\Controller;

use App\Form\SolicitudFormType;
use App\Form\FilterCategorySolicitudFormType;
use App\Entity\Solicitud;
use App\Form\SolicitudType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SolicitudController extends AbstractController
{
    #[Route('nuevaSolicitud', name: 'nuevaSolicitud')]
    public function newSolicitud(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(SolicitudType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $solicitud = $form->getData();
            $user = $this->getUser();
            $user->addSolicitude($solicitud);
            $em->persist($solicitud);
            $em->flush();
            return $this->redirectToRoute('misSolicitudes');
        }
        return $this->renderForm('/solicitudes/createsolicitud.html.twig', ['solicitudForm' => $form]);
    }

    #[Route('misSolicitudes', name: 'misSolicitudes')]
    public function listSolicitudes(EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $solicitudes = $user->getSolicitudes();
        return $this->render('/solicitudes/mySolicitudes.html.twig', ['solicitudes' => $solicitudes]);
    }

   
}