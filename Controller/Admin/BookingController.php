<?php

namespace Plugin\TPSBooking\Controller\Admin;

use Eccube\Controller\AbstractController;
use Plugin\TPSBooking\Form\Type\Admin\ConfigType;
use Plugin\TPSBooking\Form\Type\Admin\BookingSearchType;
use Plugin\TPSBooking\Entity\TPSBooking;
use Plugin\TPSBooking\Repository\ConfigRepository;
use Plugin\TPSBooking\Repository\TPSBookingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\CustomerRepository;

class BookingController extends AbstractController
{
    private $configRepository;
    private $bookingRepository;

    /**
     * Constructor function
     *
     * @return void
     */
    public function __construct(ConfigRepository $configRepository, TPSBookingRepository $bookingRepository, ProductRepository $productRepository, CustomerRepository $customerRepository)
    {
        $this->configRepository = $configRepository;
        $this->bookingRepository = $bookingRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->config = $configRepository->get();
    }

    /**
     * @Route("/%eccube_admin_route%/tpsbooking/booking", name="tpsbooking_admin_booking")
     * @Template("@TPSBooking/admin/index.twig")
     */

    public function index(Request $request, PaginatorInterface $paginator)
    {
        $builder = $this->formFactory->createBuilder(BookingSearchType::class);
        $searchForm = $builder->getForm();
        $searchData = [];
        if ($request->getMethod() == 'POST') {
            $searchForm->handleRequest($request);
            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();
            }
        }
        $qb = $this->bookingRepository->getQueryBuilderBySearchData($searchData);
        $page_no = $request->get('page_no', 1);
        $page_count = $request->get('page_count', 10);
        $pagination = $paginator->paginate(
            $qb,
            $page_no,
            $page_count
        );
        return [
            'pagination' => $pagination,
            'searchForm' => $searchForm->createView()
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
            'status' => $booking->getStatus(),
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
        $data = $request->request->all();
        $booking = new TPSBooking();
        if (isset($data['id'])) {
            $booking = $this->bookingRepository->find($data['id']);
        }
        $date = $request->get('date') . ' ' . $request->get('time');
        $dateTime = \DateTime::createFromFormat('d-m-Y H:i', $date);
        $booking->setStatus($request->get('status'));
        $booking->setNote($request->get('note'));
        $booking->setDatetime($dateTime);
        $booking->setProduct($this->productRepository->find($request->get('product_id')));
        $booking->setEmail($request->get('email'));
        $booking->setPhoneNumber($request->get('phone_number'));
        if ($request->get('customer_id')) {
            $booking->setCustomer($this->customerRepository->find($request->get('customer_id')));
        }
        $this->entityManager->persist($booking);
        $this->entityManager->flush();
        return $this->redirectToRoute('tpsbooking_admin_booking');
    }
}
