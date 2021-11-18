<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
// ...

class LuckyController extends AbstractController
{
    public function number(int $nb): Response
    {
        return $this->render('lucky/number.html.twig', [
            'nb' => $nb,
        ]);
    }

    public function numberRandom(): Response
    {
        $nb = random_int(0,100);

        return $this->render('lucky/number.html.twig', [
            'nb' => $nb,
        ]);
    }
}