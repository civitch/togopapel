<?php


namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use function Complex\sec;

class ExceptionListener extends AppListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event

        $exception = $event->getThrowable();
        $message = sprintf(
            'My Error says: %s with code: %s',
            $exception->getMessage(),
            $exception->getCode()
        );

        // Customize your response object to display the exception details
        $response = new Response();
        //$response->setContent($message);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            switch ($exception->getStatusCode()){
                case Response::HTTP_NOT_FOUND:
                    $response->setStatusCode($exception->getStatusCode());
                    $response->headers->replace($exception->getHeaders());
                    $response->setContent($this->twig->render(
                        'Error/user.html.twig',
                        ['status' => $exception->getStatusCode(), 'message' => 'Page Introuvable']
                    ));
                    break;
                case Response::HTTP_FORBIDDEN:
                    $response->setStatusCode($exception->getStatusCode());
                    $response->headers->replace($exception->getHeaders());
                    if(
                        $this->security->getUser()->hasRole($this->appSecurity->getRole('particular')) ||
                        $this->security->getUser()->hasRole($this->appSecurity->getRole('pro'))
                    )
                    {
                        $response->setContent($this->twig->render(
                            'Error/user.html.twig',
                            ['status' => $exception->getStatusCode(), 'message' => 'Accès refusé']
                        ));
                    }else{
                        $response->setContent($this->twig->render(
                            'Error/admin.html.twig',
                            ['status' => $exception->getStatusCode(), 'message' => 'Accès refusé']
                        ));
                    }
                    break;
                default:
                    $response->setStatusCode($exception->getStatusCode());
                    $response->headers->replace($exception->getHeaders());
                    $response->setContent($message);
                    break;
            }
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}
