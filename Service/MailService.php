<?php

namespace Plugin\TPSBooking\Service;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\MailHistory;
use Eccube\Entity\MailTemplate;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Shipping;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\MailHistoryRepository;
use Eccube\Repository\MailTemplateRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Plugin\TPSBooking\Entity\TPSBooking;
use Symfony\Component\Translation\TranslatorInterface;

class MailService
{
    const MAIL_TEMPLATE_BOOKING_NEW = '@TPSBooking/email/booking/new.twig';
    const MAIL_TEMPLATE_BOOKING_CONFIRM = '@TPSBooking/email/booking/confirm.twig';
    const MAIL_TEMPLATE_BOOKING_CANCEL = '@TPSBooking/email/booking/cancel.twig';
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var MailTemplateRepository
     */
    protected $mailTemplateRepository;

    /**
     * @var MailHistoryRepository
     */
    private $mailHistoryRepository;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var \Twig_Environment
     */
    protected $twig;
    protected $translator;

    /**
     * MailService constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param MailTemplateRepository $mailTemplateRepository
     * @param MailHistoryRepository $mailHistoryRepository
     * @param BaseInfoRepository $baseInfoRepository
     * @param EventDispatcherInterface $eventDispatcher
     * @param \Twig_Environment $twig
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        \Swift_Mailer $mailer,
        TranslatorInterface $translator,
        MailTemplateRepository $mailTemplateRepository,
        MailHistoryRepository $mailHistoryRepository,
        BaseInfoRepository $baseInfoRepository,
        EventDispatcherInterface $eventDispatcher,
        \Twig_Environment $twig,
        EccubeConfig $eccubeConfig
    ) {
        $this->mailer = $mailer;
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->mailHistoryRepository = $mailHistoryRepository;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->eventDispatcher = $eventDispatcher;
        $this->eccubeConfig = $eccubeConfig;
        $this->twig = $twig;
        $this->translator = $translator;
    }

    public function sendmailBooking(TPSBooking $Booking) {
        $subject = $this->translator->trans('email.tpsbooking.titles.booking_new_title') . ' #' . $Booking->getId();
        $template = self::MAIL_TEMPLATE_BOOKING_NEW;
        switch($Booking->getStatus()) {
            case TPSBooking::STATUS_NEW:
                break;
            case TPSBooking::STATUS_CONFIRMED:
                $subject = $this->translator->trans('email.tpsbooking.titles.booking_confirm_title') . ' #' . $Booking->getId();
                $template = self::MAIL_TEMPLATE_BOOKING_CONFIRM;
                break;
            case TPSBooking::STATUS_CANCELLED:
                $subject = $this->translator->trans('email.tpsbooking.titles.booking_cancel_title') . ' #' . $Booking->getId();
                $template = self::MAIL_TEMPLATE_BOOKING_CANCEL;
                break;
        }
        $message = (new \Swift_Message())
            ->setSubject('['.$this->BaseInfo->getShopName().'] '. $subject)
            ->setFrom([$this->BaseInfo->getEmail01() => $this->BaseInfo->getShopName()])
            ->setTo([$Booking->getEmail()])
            ->setBcc($this->BaseInfo->getEmail01())
            ->setReplyTo($this->BaseInfo->getEmail03())
            ->setReturnPath($this->BaseInfo->getEmail04());
        $body = $this->twig->render($template, [
            'Subject' => $subject,
            'Booking' => $Booking,
            'BaseInfo' => $this->BaseInfo,
        ]);
        $message
            ->setContentType('text/plain; charset=UTF-8')
            ->setBody($body, 'text/html');
        $count = $this->mailer->send($message);
        return $count;
    }
}
