<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailTestController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function index(MailerInterface $mailer): Response
    {
        try {
            $email = (new Email())
                ->from('noreply@example.com')
                ->to('test@example.com')
                ->subject('Test Email - ' . date('Y-m-d H:i:s'))
                ->text('Test email envoyé depuis Symfony')
                ->html('<p>Test email envoyé depuis Symfony</p>');

            $mailer->send($email);

            return new Response(
                'Email envoyé avec succès !<br>Vérifiez MailHog sur http://localhost:8025'
            );
        } catch (\Exception $e) {
            return new Response(
                'Erreur lors de l\'envoi : ' . $e->getMessage() . 
                '<br>Trace : ' . $e->getTraceAsString(), 
                500
            );
        }
    }
}