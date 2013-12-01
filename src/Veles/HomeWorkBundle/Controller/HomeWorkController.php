<?php

namespace Veles\HomeWorkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;
use Veles\HomeWorkBundle\Entity\Gbook;
use Veles\HomeWorkBundle\Form\Type\GbookType;

class HomeWorkController extends Controller
{
    protected function createPageObject($pageName)
    {
        $pBase = array(
            'main' => array(
                'header' => 'Latest Reviews',
                'content' => 'main'
            ),
            'comment' => array(
                'header' => 'Single Comment',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'
            ),
            'success' => array(
                'header' => '',
                'content' => 'Congratulations. Your comment has successfully added!'
            )
        );

        $pageData = array(
            'pageName' => $pageName,
            'pageContent' => $pBase[$pageName]
        );

        return $pageData;
    }

    public function mainAction(Request $request)
    {
        $pageData = $this->createPageObject("main");
        $commentsQuery = $this->getCommentsAction();

        $paginator  = $this->get('knp_paginator');
        $p_options = Yaml::parse($this->getYmlFile());

        $pagination = $paginator->paginate(
            $commentsQuery,
            $this->get('request')->query->get('page', $p_options['start_from']),$p_options['posts_per_page']
        );

        $pageData['comments'] = $commentsQuery;
        $pageData['pagination'] = $pagination;

        $gbook = new Gbook();
        $form = $this->createForm(new GbookType(), $gbook);
        $form->handleRequest($request);
        $pageData['form'] = $form->createView();

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($gbook);
            $manager->flush();

            return $this->redirect($this->generateUrl('_comment_success'));
        }

        return $this->render('VelesHomeWorkBundle:HomeWork:main.html.twig', $pageData);
    }

    public function oneCommentAction($cid = "")
    {
        $pageData = $this->createPageObject("comment");
        if($cid == ""){
            $pageData['oneComment']['error'] = "No comment id =( ";
        }else{
            $pageData['oneComment'] = $this->getCommentsAction($cid);
        }

        return $this->render('VelesHomeWorkBundle:HomeWork:comment.html.twig', $pageData);
    }

    public function getCommentsAction($cid = "")
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('VelesHomeWorkBundle:Gbook');

        if($cid == ""){
            $commentObj = $repository->findAllOrderedById();
        }else{
            $commentObj = $repository->find($cid);
        }

        if (!$commentObj) {
            throw $this->createNotFoundException('No comments found =(');
        }

        return $commentObj;
    }

    public function successAddAction()
    {
        $pageData = $this->createPageObject("success");
        return $this->render('VelesHomeWorkBundle:HomeWork:success.html.twig', $pageData);
    }

    protected function getYmlFile()
    {
        return __DIR__ . '/../Resources/config/paginator.yml';
    }
}