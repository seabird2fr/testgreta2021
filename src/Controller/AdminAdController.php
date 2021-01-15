<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page}", name="admin_ads_index" , requirements={"page"="[0-9]{1,}"})
     */
    public function index(AdRepository $repo,$page=1)
    {


    	$limit = 3; //on veut 5 enregistrements par page


    	$start = $limit * $page - $limit; // calcul de l'offset


    	$total = count($repo->findAll());// nbrs total d'annonces

		
		$pages= ceil($total/$limit); // nombres de pages total arrondi à l'entier supérieur   	



    	//$ads=$repo->findAll();
    	
        return $this->render('admin/ad/index.html.twig', [
            'ads'=>$repo->findBy([],[],$limit,$start),
            'page'=>$page,
            'pages'=>$pages
        ]);
    }
}
