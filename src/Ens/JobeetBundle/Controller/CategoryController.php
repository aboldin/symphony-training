<?php

namespace Ens\JobeetBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ens\JobeetBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Request;

/**
 * Category controller.
 *
 */
class CategoryController extends Controller
{
    public function showAction($slug, $page)
    {
        $translated = $this->get('translator')->trans('Symfony is great');
        var_dump($translated);
        die();
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('EnsJobeetBundle:Category')->findOneBySlug($slug);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $category->setActiveJobs($em->getRepository('EnsJobeetBundle:Job')->getActiveJobs($category->getId()));
        $jobsCollection = $category->getActiveJobs();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $jobsCollection, /* query NOT result */
            (int)$page/*page number*/,
            $this->container->getParameter('max_jobs_on_category')/*limit per page*/
        );
        return $this->render('EnsJobeetBundle:Category:show.html.twig', array(
            'category' => $category,
            'pagination' => $pagination
        ));
    }
}