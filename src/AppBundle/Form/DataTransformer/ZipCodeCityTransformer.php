<?php
namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\CityZip;
use AppBundle\Repository\CityZipRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ZipCodeCityTransformer implements DataTransformerInterface 
{
    /**
     * @var CityZipRepository $cityZipRepository
     */
    private $cityZipRepository;
    
    function __construct(CityZipRepository $cityZipRepository) {
        $this->cityZipRepository = $cityZipRepository;
    }

    
    public function reverseTransform($value) {
        
        $params = explode(' ', $value);
        
        $zipCode = $this->cityZipRepository->findOneBy(['zipCode' => $params[0]]);
        
        if (!$zipCode) {
            throw new TransformationFailedException(sprintf('No zip code city found with zip code "%s"', $value));
        }
        
        return $zipCode;
    }

    public function transform($value) {
        
        if (null === $value) {
            return '';
        }
        
        $cityZip = $this->cityZipRepository->find($value);
        
        //die($value);
        
        if (!$cityZip instanceof CityZip) {
            //return '';
            throw new \LogicException('The ZipCitySelectType can only be used with CityZip objects');
        }
        
        return $cityZip->getZipCodeCity();
    }

}
