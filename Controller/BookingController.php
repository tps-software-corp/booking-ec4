<?php

namespace Plugin\TPSBooking\Controller;

use Eccube\Controller\AbstractController;
use Plugin\TPSBooking\Form\Type\Admin\ConfigType;
use Plugin\TPSBooking\Entity\TPSBooking;
use Plugin\TPSBooking\Event;
use Plugin\TPSBooking\Repository\ConfigRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Event\EventArgs;

class BookingController extends AbstractController
{
    /**
     * @var ConfigRepository
     */
    protected $configRepository;
    protected $productRepository;
    protected $customerRepository;

    /**
     * ConfigController constructor.
     *
     * @param ConfigRepository $configRepository
     */
    public function __construct(ConfigRepository $configRepository, ProductRepository $productRepository, CustomerRepository $customerRepository)
    {
        $this->configRepository = $configRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @Route("/tpsbooking/book/", name="tps_admin_config", methods={"GET"})
     */
    public function index(Request $request)
    {
        $data = [];
        return $this->json($data);
    }

    /**
     * @Route("/tpsbooking/submit", name="front_tpsbooking_submit", methods={"POST"})
     *
     * @return json
     */
    public function submit(Request $request)
    {
        $result = [
            'error' => 1,
        ];
        $date = $request->get('date');
        $dateTime = \DateTime::createFromFormat('d/m/Y', $date);
        $Booking = new TPSBooking();
        $Booking->setStatus(TPSBooking::STATUS_NEW);
        $Booking->setDatetime($dateTime);
        $Booking->setProduct($this->productRepository->find($request->get('product_id')));
        $Booking->setEmail($request->get('email'));
        $Booking->setPhoneNumber($request->get('phone_number'));
        $Booking->setStatus(TPSBooking::STATUS_NEW);
        if ($request->get('customer_id')) {
            $Booking->setCustomer($this->customerRepository->find($request->get('customer_id')));
        }
        $this->entityManager->persist($Booking);
        $this->entityManager->flush();
        $event = new EventArgs(
            [
                'Booking' => $Booking,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(Event::TPSBOOKING_EVENT_BOOKING_CREATED, $event);
        $result['error'] = 0;
        return $this->json($result);
    }
}
