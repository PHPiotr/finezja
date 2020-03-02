<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ContactController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'attr' => ['maxlength' => 50, 'class' => 'form-control', 'placeholder' => 'Imię'],
                'constraints' => [
                    new NotBlank(),
                    new Length(array('max' => 50)),
                ],
            ])
            ->add('email', TextType::class, [
                'attr' => ['maxlength' => 255, 'class' => 'form-control', 'placeholder' => 'E-mail'],
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                    new Length(array('max' => 255)),
                ],
            ])
            ->add('phone', TextType::class, [
                'attr' => ['maxlength' => 50, 'class' => 'form-control', 'placeholder' => 'Telefon'],
                'constraints' => [
                    new NotBlank(),
                    new Length(array('max' => 50)),
                ],
            ])
            ->add('message', TextareaType::class, [
                'attr' => ['maxlength' => 1000, 'placeholder' => 'Wiadomość'],
                'constraints' => [
                    new NotBlank(),
                    new Length(array('max' => 1000)),
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Wyślij'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = $this->renderView('email/contact.html.twig', $data);
            $to = $this->getParameter('company_email');
            $subject = $this->getParameter('company_name');;
            $emailFrom = $data['email'];
            $headers[] = sprintf('From: %s', $emailFrom);
            $headers[] = sprintf('Reply-To: %s', $emailFrom);
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $sent = mail($to, $subject, $message, implode("\r\n", $headers), '-f ' . $emailFrom);
            if ($sent) {
                $this->addFlash('success', 'Dziękujemy. Twoja wiadomość została wysłana.');
            } else {
                $this->addFlash('danger', 'Przepraszamy. Wystąpił błąd. Prosimy spróbować później.');
            }
            return $this->redirectToRoute('contact');
        }
        return $this->render('contact.html.twig', [
            'active' => 'contact',
            'company_phone' => $this->getParameter('company_phone'),
            'company_email' => $this->getParameter('company_email'),
            'company_street' => $this->getParameter('company_street'),
            'company_zip_code' => $this->getParameter('company_zip_code'),
            'company_city' => $this->getParameter('company_city'),
            'form' => $form->createView(),
            'metaTitle' => 'Kontakt',
            'metaDescription' => 'ul. Kubsza 23; 44-300 Wodzisław Śl.; tel. 505-715-989.',
            'metaKeywords' => 'kubsza,wodzisław,44-300,kubsza 23,505-715-989,',
        ]);
    }
}
