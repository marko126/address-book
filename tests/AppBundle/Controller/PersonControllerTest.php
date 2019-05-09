<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PersonControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    /**
     * set Up the test 
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/people.html');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('List of all contacts in address book', $crawler->filter("#content")->text());
    }
    
    public function testCreate()
    {
        $client = static::createClient();
        
        $crawler = $client->request("GET", "/person/create");
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form = $crawler->selectButton("person_save")->form();
        $form->setValues($this->getTestData());
        
        $crawler = $client->submit($form);
        
        $this->assertTrue($client->getResponse()->isRedirect("/people.html"));
        $this->assertEquals(0, $crawler->filter(".form-error-message")->count());
    }
    
    public function testEdit()
    {
        $client = static::createClient();
        
        $person = $this->em->getRepository('AppBundle:Person')->findOneBy([], ['id' => 'desc']);
        
        $crawler = $client->request("GET", "/person/edit/" . $person->getId());
        
        if (!$client->getResponse()->isNotFound()) {
            $form = $crawler->selectButton("person_save")->form();
            $form->setValues($this->getTestData());
            $crawler = $client->submit($form);
            $this->assertTrue($client->getResponse()->isRedirect("/people.html"));
        }
    }
    
    public function testDelete()
    {
        $client = static::createClient();
        
        $person = $this->em->getRepository('AppBundle:Person')->findOneBy([], ['id' => 'desc']);
        
        $client->request("POST", "/person/delete", ['id' => 3], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest'], json_encode(['id' => 3]));
        
        $this->assertJsonResponse($client->getResponse(), 200, false);
    }
    
    /**
     * @return array
     */
    private function getTestData() {
        return [
            "person[firstName]" => "testfirstname",
            "person[lastName]" => "testlastname",
            "person[street]" => "teststreet",
            "person[cityZip]" => "10115 Berlin",
            "person[email]" => "testemail@test.com",
            "person[phoneNumber]" => "060555888",
            "person[birthday]" => [
                "day" => 1,
                "month" =>  1,
                "year" =>  1980
            ]
        ];
    }
    
    /**
     * @param Response $response
     * @param type $statusCode
     */
    protected function assertJsonResponse(Response $response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }
}
