<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Form\ZipCitySelectType;

class PersonType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder
                ->add('firstName', TextType::class)
                ->add('lastName', TextType::class)
                ->add('street', TextType::class)
                ->add('country', CountryType::class, [
                    'attr' => ['class' => 'bs-select'],
                    'mapped' => false
                ])
                ->add('cityZip', ZipCitySelectType::class)
                ->add('email', EmailType::class)
                ->add('phoneNumber', TelType::class)
                ->add('birthday', BirthdayType::class)
                ->add('save', SubmitType::class, ['label' => 'Save']);
    }
    
}
