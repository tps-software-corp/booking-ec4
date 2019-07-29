<?php

namespace Plugin\TPSBooking;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Eccube\Event\EventArgs;
use Plugin\TPSBooking\Entity\TPSBooking;
use Plugin\TPSBooking\Service\MailService;

class Event implements EventSubscriberInterface
{
    const TPSBOOKING_EVENT_BOOKING_CREATED = 'tpsbooking_event_booking_created';
    const TPSBOOKING_EVENT_BOOKING_UPDATED = 'tpsbooking_event_booking_updated';

    public function __construct(MailService $mailService) {
        $this->mailService = $mailService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            self::TPSBOOKING_EVENT_BOOKING_CREATED => 'onBookingCreated',
            self::TPSBOOKING_EVENT_BOOKING_UPDATED => 'onBookingUpdated',

        ];
    }

    public function onBookingCreated(EventArgs $event, $request)
    {
        $this->_sendBookingEmail($event['Booking']);
    }

    public function onBookingUpdated(EventArgs $event, $request)
    {
        $this->_sendBookingEmail($event['Booking']);
    }

    protected function _sendBookingEmail(TPSBooking $booking)
    {
        $this->mailService->sendmailBooking($booking);
    }
}
