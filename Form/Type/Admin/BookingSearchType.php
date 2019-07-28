<?php

namespace Plugin\TPSBooking\Form\Type\Admin;

use Plugin\TPSBooking\Entity\TPSBooking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTime;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Eccube\Form\Type\Master\CustomerStatusType;
use Eccube\Repository\Master\CustomerStatusRepository;
use Eccube\Entity\Master\CustomerStatus;


class BookingSearchType extends AbstractType
{
    private $customerStatusRepository;
    public function __construct(CustomerStatusRepository $customerStatusRepository) {
        $this->customerStatusRepository = $customerStatusRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('multi', TextType::class, [
                'label' => 'admin.tpsbooking.multi_search_label',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 100]),
                ],
            ])
            ->add('booking_time', DateType::class, [
                'label' => 'admin.tpsbooking.booking_time',
                'required' => false,
            ])
            ->add('status_new', CheckboxType::class, [
                'label' => 'admin.tpsbooking.status_new',
                'required' => false,
            ])
            ->add('status_confirmed', CheckboxType::class, [
                'label' => 'admin.tpsbooking.status_confirmed',
                'required' => false,
            ])
            ->add('status_cancelled', CheckboxType::class, [
                'label' => 'admin.tpsbooking.status_cancelled',
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'admin_search_tpsbooking';
    }
}
