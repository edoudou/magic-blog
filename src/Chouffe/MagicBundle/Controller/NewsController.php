<?php

namespace Chouffe\MagicBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Chouffe\MagicBundle\Entity\News;
use Chouffe\MagicBundle\Form\NewsType;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;

class NewsController extends Controller
{

    // TODO
    public function fetchRangeAction($number)
    {
        $response = new Response(); 
        $response->headers->set('Content-Type', 'application/json');
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ChouffeMagicBundle:News');
        // TODO: order by Desc id
        $newsList = $repo->findAll();
        
        $json = array();
        // TODO : limit the range
        foreach( $newsList as $news )
        {
            $json[] = array('title' => $news->getTitle(), 'date' => $news->getDate(), 'content' => $news->getContent());
        }

        $response->setContent(json_encode($json));
        return $this->render('ChouffeMagicBundle:Default:news.html.twig', array('newsList' => $newsList));
        
        // return $response;

    }
    
    public function fetchAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ChouffeMagicBundle:News');
        $newsList = $repo->findBy(array(), array('id' => 'desc'));

        $news = new News();
        $form = $this->createForm(new NewsType, $news);

        return $this->render('ChouffeMagicBundle:Default:news.html.twig', array('page' => 'home', 'form' => $form->createView(), 'newsList' => $newsList));
    }
    
    public function fetchAction(News $news)
    {
        $response = new Response();        
        $response->headers->set('Content-Type', 'application/json');
        $json = array('title' => $news->getTitle(), 'date' => $news->getDate(), 'content' => $news->getContent());
        $response->setContent(json_encode($json));
        
        return $response;
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function addAction()
    {
        $news = new News();
        $form = $this->createForm(new NewsType, $news);

        // La gestion d'un formulaire est particulière, mais l'idée est la suivante 
        if( $this->get('request')->getMethod() == 'POST' )
        {
            // Ici, on s'occupera de la création et de la gestion du formulaire
            $request = $this->get('request');
            $form->bind($request);
        
            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();
        
            $this->get('session')->getFlashBag()->add('notice', 'News added');
            return new Response('1');
        }

        return new Response('0');
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function updateAction(News $news)
    {
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction(News $news)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($news);
        
        $response = new Response('1');        
        $em->flush();
        return $response;
    }

    public function indexAction($name)
    {
        return $this->render('ChouffeMagicBundle:Default:index.html.twig', array('name' => $name));
    }

}

