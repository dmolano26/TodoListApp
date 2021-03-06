<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Entity\Actividad;
use App\Entity\Categoria;


class ActividadController extends AbstractController
{
    /**
     * @Route("/actividad", name="actividad_list")
     * Autor: Diego Molano
     * Fecha: 17 Mayo 2019
     * Descripción: Controlador para el componente de actividad
     */
    public function index(Request $request)
    {
        $actividades = $this->getDoctrine()->getRepository(Actividad::class)->findAll();
        $form = $this->createFormBuilder()
        ->add(
            'actividad', EntityType::class, [
                'class' => Actividad::class,
                'placeholder' => 'Busqueda de actividad',
                'attr' => array(
                    'class' => 'form-control select2 mr-sm-2'
                ),
                'choice_label' => function(Actividad $actividad) {
                    return sprintf('(%d) %s', $actividad->getId(), $actividad->getNombre());
                },
            ],
        )
        ->add(
            'buscar', ButtonType::class, array(
                'label' => 'Buscar',
                'attr' => array(
                    'class' => 'btn btn-success my-2 my-sm-0',
                    'onclick' => 'buscar_actividad();'
                )
            )
        )
        ->getForm();

        $form->handleRequest($request);
        return $this->render('actividad/index.html.twig', [
            'controller_name' => 'actividadController',
            'actividades' => $actividades,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/actividad/new", name="new_actividad")
     * Method({"GET", "POST"})
     * Autor: Diego Molano
     * Fecha: 17 Mayo 2019
     * Descripción: Función usada para crear un nuevo registro de actividad.
     */
    public function new_actividad(Request $request) {
        $actividad = new Actividad();

        $form = $this->createFormBuilder($actividad)
            ->add(
                'nombre', TextType::class, array(
                    'attr' => array(
                        'class' => 'form-control'
                    )   
                )
            )
            ->add(
                'descripcion', TextareaType::class, array(
                    'attr' => array(
                        'class' => 'form-control'
                    )
                )
            )
            ->add(
                'categoria', EntityType::class, [
                    'class' => Categoria::class,
                    'placeholder' => 'Escoja una categoria',
                    'attr' => array(
                        'class' => 'form-control'
                    ),
                    'choice_label' => function(Categoria $categoria) {
                        return sprintf('(%d) %s', $categoria->getId(), $categoria->getNombre());
                    },
                ],
            )
            ->add(
                'estado', ChoiceType::class, [
                    'choices'  => [
                        'PENDIENTE' => 0,
                        'REALIZADA' => 1
                    ],
                    'attr' => array(
                        'class' => 'form-control'
                    )
                ]                      
            )
            ->add(
                'guardar', SubmitType::class, array(
                    'label' => 'Crear',
                    'attr' => array('class' => 'btn btn-primary mt-3')
                )
            )
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $actividad = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($actividad);
                $entityManager->flush();

                return $this->redirectToRoute('actividad_list');
            }

            return $this->render('actividad/new.html.twig', array(
                'form' => $form->createView()
            ));
    }

    /**
     * @Route("/actividad/edit/{id}", name="edit_actividad")
     * Method({"GET", "POST"})
     * Autor: Diego Molano
     * Fecha: 17 Mayo 2019
     * Descripción: Función usada para editar un registro de actividad.
     */
    public function edit_actividad(Request $request, $id) {
        $actividad = new Actividad();
        $actividad = $this->getDoctrine()->getRepository(Actividad::class)->find($id);

        $form = $this->createFormBuilder($actividad)
        ->add(
            'nombre', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control'
                )   
            )
        )
        ->add(
            'descripcion', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control'
                )
            )
        )
        ->add(
            'categoria', EntityType::class, [
                'class' => Categoria::class,
                'placeholder' => 'Escoja una categoria',
                'attr' => array(
                    'class' => 'form-control'
                ),
                'choice_label' => function(Categoria $categoria) {
                    return sprintf('(%d) %s', $categoria->getId(), $categoria->getNombre());
                },
            ],
        )
        ->add(
            'estado', ChoiceType::class, [
                'choices'  => [
                    'PENDIENTE' => 0,
                    'REALIZADA' => 1
                ],
                'attr' => array(
                    'class' => 'form-control'
                )
            ]                      
        )
        ->add(
            'guardar', SubmitType::class, array(
                'label' => 'Crear',
                'attr' => array('class' => 'btn btn-primary mt-3')
            )
        )
        ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                return $this->redirectToRoute('actividad_list');
            }

            return $this->render('actividad/edit.html.twig', array(
                'form' => $form->createView()
            ));
    }

    /**
     * @Route("/actividad/{id}", name="ver_actividad")
     * Autor: Diego Molano
     * Fecha: 17 Mayo 2019
     * Descripción: Función usada para ver el detalle de un registro.
     */
    public function ver_actividad($id) {
        $actividad = $this->getDoctrine()->getRepository(Actividad::class)->find($id);

        return $this->render('actividad/ver.html.twig', array('actividad' => $actividad));
    }

    /**
     * @Route("/actividad/delete_actividad/{id}", name="delete_actividad")
     * @Method({"DELETE"})
     */
    public function delete_actividad(Request $request, $id) {
        $actividad = $this->getDoctrine()->getRepository(Actividad::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($actividad);
        $entityManager->flush();

        $response = new Response();
        $response->send();
    }
}
