<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Entity\ImageUpload;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use App\Services\ImagesUploadService;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {

    	//$repo=$this->getDoctrine()->getRepository(Ad::class);// on va chercher le repository de Ad

    	$ads=$repo->findAll();// prend tous les enregistrements de la table visée.

    	//dump($ads);

        return $this->render('ad/index.html.twig', [
            'ads'=>$ads, // je transmet à twig une clé ads qui contient $ads
        ]);
    }


      /**
     * @Route("/ads/new", name="ads_create")
     * @IsGranted("ROLE_USER")
     */
    public function create(EntityManagerInterface $manager,Request $request,ImagesUploadService $upload)
    {

    	$ad = new Ad();

        $ad->setAuthor($this->getUser());

 		$form = $this->createForm(AnnonceType::class,$ad);


 		$form->handleRequest($request);
 		//dump($ad);

 		if ($form->isSubmitted() && $form->isValid())
 		{


 			$slugify=new Slugify();
    		$slug=$slugify->slugify($ad->getTitle());
    		$ad->setSlug($slug);

    		

    		foreach ($ad->getImages() as $image) {
    			$image->setAd($ad);
    			$manager->persist($image);
    		}


          // gestion des images uploadées
            $upload->upload($ad,$manager);

          


    		$manager->persist($ad);
    		$manager->flush();

    		$slug2 =$ad->getSlug().'_'.$ad->getId();
    		$ad->setSlug($slug2);

    		$manager->persist($ad);
    		$manager->flush();

    		$this->addFlash(
    					'success', 
    					'l\'annonce de titre '.$ad->getTitle().' est bien été enregistrée'
    		);

    		return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]);

 		}


 		return $this->render('ad/new.html.twig', [
 			'form'=>$form->createView(),
        ]);

	}


 /**
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()" ,message="Cette annonce ne vous appartient pas")
     */
    public function edit(EntityManagerInterface $manager,Request $request,Ad $ad,ImagesUploadService $upload)
    {

    

 		$form = $this->createForm(AnnonceType::class,$ad);


 		$form->handleRequest($request);
 		//dump($ad);

 		if ($form->isSubmitted() && $form->isValid())
 		{

            // gestion des images uploadées
            $upload->upload($ad,$manager);

            //suppression des images uploadées
            $tabid = $ad->tableau_id;
            $tabid = preg_replace('#^,#','',$tabid);
            $tabid = explode(',',$tabid);
            foreach ($tabid as $id) {

                foreach ($ad->getImageUploads() as $image) {
                        if ($id == $image->getId())
                        {
                            $manager->remove($image);
                            $manager->flush();

                            unlink($_SERVER['DOCUMENT_ROOT'].$image->getUrl());


                        }

                }
                
            }
            //dump($_SERVER);exit;

            
               // exit;

 			$slugify=new Slugify();
    		$slug=$slugify->slugify($ad->getTitle());
    		$ad->setSlug($slug);


    		//dump($ad);exit;

    		foreach ($ad->getImages() as $image) {
    			$image->setAd($ad);
    			$manager->persist($image);
    		}

    		$manager->persist($ad);
    		$manager->flush();

    		$slug2 =$ad->getSlug().'_'.$ad->getId();
    		$ad->setSlug($slug2);

    		$manager->persist($ad);
    		$manager->flush();

    		$this->addFlash(
    					'success', 
    					'l\'annonce de titre '.$ad->getTitle().' est bien été modifiée'
    		);

    		return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]);

 		}


 		return $this->render('ad/edit.html.twig', [
 			'form'=>$form->createView(),
 			'ad'=>$ad,
        ]);

	}

        /**
     * @Route("/ads/{slug}", name="ads_show")
     */
    public function show(Ad $ad)
    {

 	//$ad=$repo->findOneBySlug($slug);
  
  //dump($ad);

        return $this->render('ad/show.html.twig', [
           'ad'=>$ad,

        ]);
    }



           /**
     * @Route("/ads/{slug}/delete", name="ads_delete")
      * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()" ,message="Vous ne pouvez pas supprimer cette annonce")
     */
    public function delete(EntityManagerInterface $manager,Ad $ad)
    {

        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
                        'success', 
                        'l\'annonce de titre '.$ad->getTitle().' est bien été supprimée'
            );

            return $this->redirectToRoute('ads_index');



    }




}
