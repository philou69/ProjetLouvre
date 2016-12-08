<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Billet;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppControllerTest extends WebTestCase
{
    public function testReservation()
    {
        // création d'un client
        $client = static::createClient();

        // Création d'une requete de la page formulaire
        $crawler = $client->request('GET', '/reservation');
        // Sélection du formulaire
        $form = $crawler->selectButton('Valider')->form();

        // Remplissage du formulaire
        $form['reservation[dateReservation]'] = '15-02-2017';
        $form['reservation[demiJournee]'] = false;
        $form['reservation[email]']= 'phil.pichet@gmail.com';
        $form['reservation[billets][0][nom]'] = 'pichet';
        $form['reservation[billets][0][prenom]'] = 'philippe';
        $form['reservation[billets][0][dateNaissance][day]'] = '25';
        $form['reservation[billets][0][dateNaissance][month]'] = '8';
        $form['reservation[billets][0][dateNaissance][year]'] = '1987';
        $form['reservation[billets][0][reduction]'] = false;
        $form['reservation[billets][0][pays]'] = '2';

        // Ajout d'un deuxieme billet
        $values = $form->getPhpValues();
        $values['reservation']['billets'][0]['nom'] = 'pichet';
        $values['reservation']['billets'][0]['prenom'] = 'clotilde';
        $values['reservation']['billets'][0]['dateNaissance']['day'] = '25';
        $values['reservation']['billets'][0]['dateNaissance']['month'] = '10';
        $values['reservation']['billets'][0]['dateNaissance']['year'] = '1990';
        $values['reservation']['billets'][0]['reduction'] = false;
        $values['reservation']['billets'][0]['pays'] = '2';

        $client->request($form->getMethod(), $form->getUri(), $values);
        $client->followRedirect();
        $this->assertEquals('AppBundle\Controller\AppController::confirmationAction', $client->getRequest()->attributes->get('_controller'));
    }
}
