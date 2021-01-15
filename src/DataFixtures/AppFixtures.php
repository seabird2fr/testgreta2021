<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

     private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName("Eric")
                ->setLastName("Devolder")
                ->setEmail("eric.devolder@ac-nice.fr")
                ->setPicture("https://via.placeholder.com/64")
                ->setIntroduction("Je suis dieu")
                ->setDescription("C'est moi le chef")
                ->setSlug("eric-devolder")
            ->setHash($this->passwordEncoder->encodePassword(
             $adminUser,
             'password'
         ))
           ->addUserRole($adminRole);
           
           $manager->persist($adminUser); 

       

      for ($k=1; $k<=5; $k++){

            $user = new User();
            $user->setFirstName("prénom$k")
                ->setLastName("Nom$k")
                ->setEmail("test$k@test.fr")
                ->setPicture("https://via.placeholder.com/64")
                ->setIntroduction("introduction $k")
                ->setDescription("Description $k")
                ->setSlug("prenom$k-nom$k")

            ->setHash($this->passwordEncoder->encodePassword(
             $user,
             'password'
         ));
                
                

                $manager->persist($user);
                $manager->flush();

                $slug2 =$user->getSlug().'_'.$user->getId();
                $user->setSlug($slug2);

                $manager->persist($user);


        // annonces
    	for ($i=0; $i < mt_rand(1,4) ; $i++) { 

    		$slugify=new Slugify();
    		$title="Titre de l'annonce n°: $i";
    		$slug=$slugify->slugify($title);

    		$ad = new Ad();
    		$ad->setTitle("Titre de l'annonce n°: $i")
    			->setSlug($slug)
    			->setPrice(mt_rand(40,200))
    			->setIntroduction("introduction de <strong><i>l'annonce n°: $i</i></strong>")
    			->setContent("contenu de <strong>l'annonce n°: $i</strong>")
    			->setRooms(mt_rand(1,5))
    			->setCoverImage("https://via.placeholder.com/350")
                ->setAuthor($user);

    		for ($j=0; $j < mt_rand(2,5) ; $j++) { 
    			
    			$image = new Image();
    			$image->setUrl("https://via.placeholder.com/350");
    			$image->setCaption("légende de l'image $j");
    			$image->setAd($ad);

    			$manager->persist($image);

    			}	



    			$manager->persist($ad);
    			$manager->flush();

    			//dump($ad->getId());
    			$slug2 =$ad->getSlug().'_'.$ad->getId();
    			$ad->setSlug($slug2);

    			$manager->persist($ad);
    			$manager->flush();


    	}


}

        //$manager->flush();
    }
}
