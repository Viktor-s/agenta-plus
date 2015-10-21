<?php

namespace AgentPlus\Controller\Cabinet;

use Symfony\Component\HttpFoundation\Response;

class CabinetController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Construct
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * App action
     *
     * @return Response
     */
    public function app()
    {
        $content = $this->twig->render('Cabinet/app.html.twig');

        return new Response($content);
    }
}