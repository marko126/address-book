<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\City;

/**
 * CityZip
 *
 * @ORM\Table(name="city_zip")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CityZipRepository")
 * @UniqueEntity(
 *      fields={"zipCode", "city"}, 
 *      message="The zip_code for given city already exists"
 * )
 */
class CityZip
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="zip_code", type="string", length=10)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="City", inversedBy="cityZip")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="city", referencedColumnName="id")
     * })
     */
    private $city;
    
    /**
     * @var type 
     * 
     * @Orm\OneToMany(targetEntity="Person", mappedBy="cityZip")
     */
    private $person;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return CityZip
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }
    
    /**
     * Get zipCodeCity
     *
     * @return string
     */
    public function getZipCodeCity()
    {
        return $this->zipCode . ' ' . $this->getCity()->getName();
    }

    /**
     * Set city
     *
     * @param City $city
     *
     * @return CityZip
     */
    public function setCity(City $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return City
     */
    public function getCity(): City
    {
        return $this->city;
    }
}

