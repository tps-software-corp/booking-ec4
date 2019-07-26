<?php

namespace Plugin\TPSBooking\Form\Type\Admin;

use Plugin\TPSBooking\Entity\TPSBooking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTime;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TPSBookingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('datetime', DateTime::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('status', TextType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('note', TextType::class);
        $builder->add('people_num', NumberType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TPSBooking::class,
        ]);
    }
}
