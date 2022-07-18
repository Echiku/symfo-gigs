<?php

namespace App\Controller;

use App\Entity\Listings;
use App\Repository\ListingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use function PHPUnit\Framework\returnSelf;

class ListingController extends AbstractController
{
      public function __construct(EntityManagerInterface $manager, ListingsRepository $listings)
      {
        $this->manager=$manager;
        $this->listing=$listings;
      }

      //Show Listings gigs

    #[Route('/', name: 'app_home', methods:'GET')]
    public function index(Request $request): Response
    {
        //handling search
        
        $query_search=$request->query->get('search');

        $query_tag=$request->query->get('tag');

        if(empty($query_search) && empty($query_tag)){

            $listings=$this->listing->findAllList();
            
            return $this->render('listing/index.html.twig', [
                'controller_name' => 'ListingController',
                'listings'=>$listings,
            
            ]);
        }

        else if($query_search){
            $listings = $this->listing->search($query_search);
            return $this->render('listing/index.html.twig', [
                'controller_name' => 'ListingController',
                'listings'=>$listings,
                
            ]);

        
        }

        else if($query_tag){
            $listings = $this->listing->search($query_tag);
            return $this->render('listing/index.html.twig', [
                'listings'=>$listings,
                
            ]);

        
        }

       
    }



    //Show create gigs 
    
    #[Route('/listings/create', name: 'create_listing', methods:'GET')]
    public function showCreate(): Response
    {
        
        return $this->render('listing/create.html.twig');
    }


    //Add new gig
    #[Route('/listings', name: 'add_listing', methods:'POST')]
    public function create(Request $request): Response
    {
       
           //Create a custom csrf to pass in the 
               $token= $request->request->get("token");

               

               if(!$this->isCsrfTokenValid('myform',$token))
               {
                return new Response('Operation not allowed', Response::HTTP_BAD_REQUEST,
                ['content-type' =>'text/plain']);
               }


              $company= $request->request->get('company');
              $title= $request->request->get('title');
              $location= $request->request->get('location');
              $email= $request->request->get('email');
              $website= $request->request->get('website');
              $tags= $request->request->get('tags');
              $logo=$request->files->get('logo');
              $description= $request->request->get('description');

              

              if(empty($company) || empty($title) || empty($location) || empty($email) || empty($website) || empty($tags) || empty($description)){


                 $company=empty($company) ? '':$company;
                 $title=empty($title) ? '':$title;
                 $location=empty($location) ? '':$location;
                 $email=empty($email) ? '':$email;
                 $website=empty($website) ? '':$website;
                 $tags=empty($tags) ? '':$tags;
                 $description=empty($description) ? '':$description;



                 return $this->render('listing/create.html.twig',[
                    'company'=>$company,
                    'title'=>$title,
                    'location'=>$location,
                    'email'=>$email,
                    'website'=>$website,
                    'tags'=>$tags,
                    'description'=>$description

                 ]);


              }

              //check email for validation              

              $checkEmail=$this->listing->findOneByEmail($email);

              $check=filter_var($email, FILTER_VALIDATE_EMAIL);

              if($checkEmail){

                $message="There is already an account with this email !";
               
                return $this->render('listing/create.html.twig',[
                   
                    'email_error'=>$message,
                
                 ]);

              }

              if($check == false){

                $message="Please enter a valid email!";

                return $this->render('listing/create.html.twig',[
                   
                    'email_error'=>$message,
                
                 ]);
              }

              //handling of logo

              if($logo){
                $filename = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = str_replace(" ", "_", $logo);
                $filename = uniqid() . "." . $logo->getClientOriginalExtension();
                $logo->move(
                    $this->getParameter('logo_directory'),
                    $filename
                );

              }

              //


              $listing= new Listings();

              $listing->setCompany($company)
                      ->setTitle($title)
                      ->setLocation($location)
                      ->setEmail($email)
                      ->setWebsite($website)
                      ->setTags($tags)
                      ->setLogo(empty($logo) ? '':"/$filename")
                      ->setDescription($description)
                      ->setUser($this->getUser());

            $this->manager->persist($listing);
            $this->manager->flush();

            //message
            $this->addFlash('notice', 'New Gig added successfully !');

             return $this->redirectToRoute('app_home');

           // 
    }

  

     // Show manage listings
     #[Route('listings/manage', name: 'manage_listing', methods:'GET')]
     public function manage(): Response
     {

        $user=$this->getUser();
        
        $listings=$this->listing->findHisListing($user);
        

        return $this->render('listing/manage.html.twig',[
            'listings'=>$listings,
        ]);

 
     }


      // Show Edit listings
      #[Route('listings/{id}/edit', name: 'showedit_listing', methods:'GET')]
      public function showEdit($id): Response
      {
        $listing=$this->listing->find($id);
        return $this->render('listing/edit.html.twig',[
            'listing'=>$listing,
        ]);
      
      }

       // Edit listings
       #[Route('/edit/{id}', name: 'edit_listing', methods:'POST')]
       public function edit(Request $request,$id): Response
       {
         
        
        $listing=$this->listing->find($id);


        $company= $request->request->get('company');
        $title= $request->request->get('title');
        $location= $request->request->get('location');
        $email= $request->request->get('email');
        $website= $request->request->get('website');
        $tags= $request->request->get('tags');
        $logo=$request->files->get('logo');
        $description= $request->request->get('description');

        //check email for validation              


        $check=filter_var($email, FILTER_VALIDATE_EMAIL);

       
        if($check == false){

          $message="Please enter a valid email!";

          return $this->render('listing/edit.html.twig',[
             
              'email_error'=>$message,
              'listing'=>$listing,

          
           ]);
        }

        //handling of logo

        if($logo){
          $filename = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
          $filename = str_replace(" ", "_", $logo);
          $filename = uniqid() . "." . $logo->getClientOriginalExtension();
          $logo->move(
              $this->getParameter('logo_directory'),
              $filename
          );

        }


        $listing->setCompany($company)
                ->setTitle($title)
                ->setLocation($location)
                ->setEmail($email)
                ->setWebsite($website)
                ->setTags($tags)
                ->setLogo(empty($logo) ? '':"$filename")
                ->setDescription($description);

      $this->manager->flush();

      //message
      $this->addFlash('notice', 'Gig updated successfully !');

    

        return $this->redirect($request->server->get('HTTP_REFERER'));
        

       }
 

      

    
    // Show a single listing

    #[Route('/listings/{id}', name: 'single_listing', methods:'GET')]
    public function show($id): Response
    {
        $listing=$this->listing->find($id);
        return $this->render('listing/show.html.twig',[
            'listing'=>$listing,
        ]);
    }

       // delete listing
       #[Route('/delete/{id}', name: 'delete_listing', methods:'POST')]
       public function delete($id): Response
       {
        $listing=$this->listing->find($id);
        
        $this->manager->remove($listing);
        $this->manager->flush();

        $this->addFlash('notice', ' Gig deleted successfully !');

        return $this->redirectToRoute('manage_listing');

       }
   


   

   
    


    

    


   


}
