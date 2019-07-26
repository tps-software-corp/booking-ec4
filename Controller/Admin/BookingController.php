<?php

namespace Plugin\TPSBooking\Controller\Admin;

use Eccube\Controller\AbstractController;
use Plugin\TPSBooking\Form\Type\Admin\ConfigType;
use Plugin\TPSBooking\Entity\TPSBooking;
use Plugin\TPSBooking\Repository\ConfigRepository;
use Plugin\TPSBooking\Repository\TPSBookingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    private $configRepository;
    private $bookingRepository;

    /**
     * Constructor function
     *
     * @return void
     */
    public function __construct(ConfigRepository $configRepository, TPSBookingRepository $bookingRepository)
    {
        $this->configRepository = $configRepository;
        $this->bookingRepository = $bookingRepository;
        $this->config = $configRepository->get();
    }

    /**
     * @Route("/%eccube_admin_route%/tpsbooking/booking", name="tpsbooking_admin_booking")
     * @Template("@TPSBooking/admin/index.twig")
     */

    public function index(Request $request)
    {
        $bookings = $this->bookingRepository->getList();
        return [
            'bookings' => $bookings
        ];
    }
    /**
     * @Route("/%eccube_admin_route%/tpsbooking/booking/info", name="tpsbooking_admin_booking_info", methods={"GET"})
     */
    public function info(Request $request)
    {
        $booking = $this->bookingRepository->find($request->get('id'));
        return $this->json([
            'id' => $booking->getId(),
            'datetime' => $booking->getDatetime(),
            'product_id' => $booking->getProduct()->getId(),
            'email' => $booking->getEmail(),
            'phone_number' => $booking->getPhoneNumber(),
            'date' => $booking->getCreateDate()->format('d-m-Y'),
            'time' => $booking->getCreateDate()->format('H:i'),
        ]);
    }

    /**
     * @Route("/%eccube_admin_route%/tpsbooking/booking/confirm", name="tpsbooking_admin_booking_confirm", methods={"POST"})
     */
    public function confirm(Request $request)
    {
        dump($request->request->all());
        die;
    }
}
