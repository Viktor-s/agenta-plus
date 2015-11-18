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
     * @var string
     */
    private $theme;

    /**
     * Construct
     *
     * @param \Twig_Environment $twig
     * @param string            $theme
     */
    public function __construct(\Twig_Environment $twig, $theme)
    {
        $this->twig = $twig;
        $this->theme = $theme;
    }

    /**
     * App action
     *
     * @return Response
     */
    public function app()
    {
        $content = $this->twig->render('Cabinet/app.html.twig', [
            'theme' => $this->theme
        ]);

        return new Response($content);
    }
}