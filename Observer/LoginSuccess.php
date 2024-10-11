<?php
declare(strict_types=1);

/**
 * KiwiCommerce
 *
 * Do not edit or add to this file if you wish to upgrade to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please contact us https://kiwicommerce.co.uk/contacts.
 *
 * @category   KiwiCommerce
 * @package    KiwiCommerce_AdminActivity
 * @copyright  Copyright (C) 2018 Kiwi Commerce Ltd (https://kiwicommerce.co.uk/)
 * @license    https://kiwicommerce.co.uk/magento2-extension-license/
 */
namespace KiwiCommerce\AdminActivity\Observer;

use KiwiCommerce\AdminActivity\Action\CheckIsAllowedLoggingHour;
use KiwiCommerce\AdminActivity\Api\LoginRepositoryInterface;
use KiwiCommerce\AdminActivity\Helper\Benchmark;
use KiwiCommerce\AdminActivity\Service\EmailSenderInterface;
use \KiwiCommerce\AdminActivity\Helper\Data as DataHelper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Event\Observer;

/**
 * Class LoginSuccess
 */
class LoginSuccess implements ObserverInterface
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var LoginRepositoryInterface
     */
    protected $loginRepository;

    /**
     * @var Benchmark
     */
    protected $benchmark;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var CheckIsAllowedLoggingHour
     */
    private $checkIsAllowedLoggingHour;

    /**
     * @var EmailSenderInterface
     */
    private $emailSender;

    /**
     * @param CheckIsAllowedLoggingHour $checkIsAllowedLoggingHour
     * @param LoginRepositoryInterface  $loginRepository
     * @param EmailSenderInterface      $emailSender
     * @param TransportBuilder          $transportBuilder
     * @param DataHelper                $dataHelper
     * @param Benchmark                 $benchmark
     * @param DateTime                  $dateTime
     */
    public function __construct(
        CheckIsAllowedLoggingHour   $checkIsAllowedLoggingHour,
        LoginRepositoryInterface    $loginRepository,
        EmailSenderInterface        $emailSender,
        TransportBuilder            $transportBuilder,
        DataHelper                  $dataHelper,
        Benchmark                   $benchmark,
        DateTime                    $dateTime
    ) {
        $this->checkIsAllowedLoggingHour    = $checkIsAllowedLoggingHour;
        $this->loginRepository              = $loginRepository;
        $this->emailSender                  = $emailSender;
        $this->transportBuilder             = $transportBuilder;
        $this->dataHelper                   = $dataHelper;
        $this->benchmark                    = $benchmark;
        $this->dateTime                     = $dateTime;
    }

    public function execute(Observer $observer)
    {
        $this->benchmark->start(__METHOD__);

        if (!$this->dataHelper->isLoginEnable()) {
            return $observer;
        }

        if ($this->dataHelper->isEnableWorkingHoursActivityLog() && !$this->checkIsAllowedLoggingHour->execute()) {
            date_default_timezone_set('Europe/Sofia');
            $currentDate = date('Y-m-d');
            $currentHour = date('H:i');
            $this->emailSender->send($currentDate, $currentHour, $observer->getUser()->getName(), $observer->getUser()->getEmail());
        }

        $this->loginRepository->setUser($observer->getUser())->addSuccessLog();
        $this->benchmark->end(__METHOD__);
    }
}
