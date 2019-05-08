<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Repository\CityZipRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Form\DataTransformer\ZipCodeCityTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class ZipCitySelectType extends AbstractType {
    
    /**
     * @var FormBuilderInterface $router 
     */
    private $router;
    
    /**
     * @var CityZipRepository $cityZipRepository
     */
    private $cityZipRepository;
    
    public function __construct(CityZipRepository $cityZipRepository, RouterInterface $router) {
        $this->cityZipRepository = $cityZipRepository;
        $this->router = $router;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->addModelTransformer(new ZipCodeCityTransformer(
            $this->cityZipRepository,
            $options['finder_callback']
        ));
    }
    
    public function getParent()
    {
        return TextType::class;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'Zip code / city not found!',
            'finder_callback' => function(CityZipRepository $cityZipRepository, string $zipCode) {
                return $cityZipRepository->findOneBy(['zipCode' => $zipCode]);
            },
            'attr' => [
                'class' => 'js-city-zip-autocomplete',
                'data-autocomplete-url' => $this->router->generate('person_get_zip_code_cities_api')
            ]
        ]);
    }
}
