<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use AppBundle\Entity\City;
use AppBundle\Entity\CityZip;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Intl;
use AppBundle\Services\FileUploader;
use AppBundle\Form\PersonType;
use AppBundle\Repository\CityZipRepository;

class PersonController extends Controller {
    
    /**
     * @param Request $request
     * @param type $_format
     * @return type
     */
    public function indexAction(Request $request, $_format = "html") 
    {
        \Locale::setDefault('en');

        $countries = Intl::getRegionBundle()->getCountryNames();
        
        if ($_format == 'json') {
            
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT p
                FROM AppBundle:Person p'
            );
            $people = $query->getResult();
            
            $return = [];
            
            foreach ($people as $key => $person) {
                /**
                 * @var AppBundle\Entity\CityZip $city
                 */
                $cityZip = $person->getCityZip();
                $city = $cityZip->getCity();
                $return[$key]['id'] = $person->getId();
                $return[$key]['firstName'] = $person->getFirstName();
                $return[$key]['lastName'] = $person->getLastName();
                $return[$key]['countryName'] = $countries[$city->getCountry()];
                $return[$key]['city'] = $city->getName();
                $return[$key]['street'] = $person->getStreet();
                $return[$key]['zip'] = $cityZip->getZipCode();
                $return[$key]['phoneNumber'] = $person->getPhoneNumber();
                $return[$key]['birthday'] = $person->getBirthday()->format('d.m.Y');
                $return[$key]['email'] = $person->getEmail();
            }

            return new JsonResponse($return);
        }
        
        $repository = $this->getDoctrine()->getRepository(Person::class);
        
        return $this->render('person/index.html.twig', [
            'people' => $repository->findAll()
        ]);
    }
    
    /**
     * @param Request $request
     * @return type
     */
    public function createAction(Request $request, FileUploader $fileUploader) {
        
        $person = new Person();
        
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $person = $form->getData();
            
            $file = $person->getPicture();
            
            if ($file) {
                $fileName = $fileUploader->upload($file);
            
                if ($fileName) {
                    $person->setPicture($fileName);
                } else {
                    $this->addFlash("warning", "Something went wrong when you try to upload the picture!");
                }
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();
            
            $this->addFlash("success", "New contact person {$person->getFullName()} has been added successfully!");
            
            return $this->redirectToRoute('person_list', ['_format' => 'html']);
        }
        
        return $this->render('person/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @param Request $request
     * @param int $id
     * @return type
     */
    public function editAction(Request $request, int $id, FileUploader $fileUploader) {
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Person');
        $person = $repository->find($id);
        
        if (empty($person)) {
            return $this->redirectToRoute('person_list', ['_format' => 'html']);
        }
        
        $oldPicture = $person->getPicture();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $person = $form->getData();
            
            $file = $person->getPicture();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                if ($fileName) {
                    $person->setPicture($fileName);
                    $fileUploader->delete($oldPicture);
                } else {
                    $this->addFlash("warning", "Something went wrong when you try to upload the picture!");
                }
            } else {
                $person->setPicture($oldPicture);
            }
            
            $em->flush();
            
            $this->addFlash("success", "The contact person {$person->getFullName()} has been changed successfully!");
            
            return $this->redirectToRoute('person_list', ['_format' => 'html']);
        }
        
        return $this->render('person/edit.html.twig', [
            'form' => $form->createView(),
            'person' => $person
        ]);
    }
    
    /**
     * @param Request $request
     * @param int $id
     * @return type
     */
    public function deleteAction(Request $request, FileUploader $fileUploader) {
        
        //print_r($request);
        //die();
        
        if (!$request->isXMLHttpRequest()) {
            return new Response('This is not ajax!', 400);
        }
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Person');
        $person = $repository->find($request->request->get('id'));
        $fileUploader->delete($person->getPicture());
        
        $em->remove($person);
        $em->flush();
        
        $return = [
            'status' => 200
        ];
        
        if ($request->isXMLHttpRequest()) {   
            $this->addFlash("success", "The contact person {$person->getFullName()} has been removed successfully!");
            return new JsonResponse($return, 200);
        }
    }


    /**
     * @param Request $request
     * @return type
     */
    public function getZipCodeCitiesApiAction(Request $request) {
        
        /**
         * @var CityZipRepository $repository
         */
        $repository = $this->getDoctrine()->getRepository(CityZip::class);
        
        $zipCodeCities = $repository->findAllMatching($request->query->get('query'), $request->query->get('country'));
        return $this->json([
            'zipCodeCities' => $zipCodeCities
        ], 200);
    }
    
}
