<?php


namespace App\Services;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class sendEmail
{
    private $mailer;
    /**
     * Propriété contenant l'environnement twig
     *
     * @var Environment
     */
    private $renderer;
    private $gitLabApi;


    public function __construct(\Swift_Mailer $mailer, Environment $renderer,GitLabApi $gitLabApi)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
        $this->gitLabApi = $gitLabApi;
    }

    public function sendmail()
    {

        try {
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('send@example.com')
                ->setTo('recipient@example.com')
                ->setBody($this->renderer->render(
                // templates/emails/registration.html.twig
                    'email/email.html.twig',[
                        'merges' => $this->gitLabApi->fetchMR(),
                    ]
                ),
                    'text/html'
                );
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }

        $this->mailer->send($message);

    }
}