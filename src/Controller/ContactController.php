<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Services\MailSender;
use App\Services\RecaptchaService;
use Flasher\Prime\FlasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ContactController extends AbstractController
{

    public function __construct(
    private SeoPageInterface $seoPage,
    private UrlGeneratorInterface $urlGenerator,
    private EntityManagerInterface $manager,
    private FlasherInterface $flasher,
    private RecaptchaService $googleRecapcha,
    private ParameterBagInterface $params,
    private MailSender $sender,
    )
    {
        
    }

    #[Route('/contactez-nous', name: 'app_contact')]
    public function contact(Request $request): Response
    {   
        /**SEO PART */
        $this->contactPageSeo();
        /** END SEO PART */

        /**CONTACT FORM PROCESS*/
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            //dd('hellow');
            $secret=$this->params->get('recapcha_secret');
            $url = "https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$form->get('captcha')->getData()}";
            
            $response = $this->googleRecapcha->curlProcess($url);

            if(empty($response) || is_null($response)){
                
                $this->flasher->addWarning("Something wrong!");

                return $this->redirectToRoute('contact');
            }else{
                $data = json_decode($response);

                if($data->success){  
                    $contact->setCreatedAt(new \DateTimeImmutable('now'));
                    $this->manager->persist($contact);
                    $this->manager->flush();

                    $this->sender->send(
                        $contact->getEmail(), 
                        $contact->getName(), 
                        $contact->getMessage(), 
                        $contact->getSubject()
                    );

                    $this->flasher->addSuccess("Votre demande a été bien prise en compte");

                    return $this->redirectToRoute('app_contact');

                }else{
                    
                    $this->flasher->addError("Confirm you are not robot!");
                    return $this->redirectToRoute('app_contact');
                }
            }

            
        }
        /**END CONTACT FORM PROCESS*/

        return $this->renderForm('contact/contact.html.twig', compact("form"));
    }

    private function contactPageSeo():void{
        $description = "la meilleures agence de conseils Rh au Cameroun";
        $this->seoPage->setTitle("Contact")
            ->addMeta('property','og:title','les petites annonces MA.BA.CE II')
            ->addTitleSuffix("CAPITAL RH SOLUTIONS")
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:title', "Contact-CAPITAL RH SOLUTIONS")
            ->setLinkCanonical($this->urlGenerator->generate('app_contact',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_contact',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('Contact', []);
    }
}
