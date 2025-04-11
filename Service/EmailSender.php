<?php
/**
 * @package KiwiCommerce\AdminActivity
 * @author  Stenik Magento Team <magedev@stenik.bg>
 */

declare(strict_types=1);

namespace KiwiCommerce\AdminActivity\Service;

use KiwiCommerce\AdminActivity\Helper\Data as DataHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\DataObject;
use Magento\Framework\App\Area;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

/**
 * Class EmailSender
 */
class EmailSender implements EmailSenderInterface
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

     /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param JournalLoggerInterface $journalLogger
     * @param ScopeConfigInterface   $scopeConfig
     * @param TransportBuilder       $transportBuilder
     * @param DataHelper             $dataHelper
     */
    public function __construct(
        ScopeConfigInterface    $scopeConfig,
        TransportBuilder        $transportBuilder,
        LoggerInterface         $logger,
        DataHelper              $dataHelper
    ) {
        $this->scopeConfig      = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->dataHelper       = $dataHelper;
        $this->logger           = $logger;
    }

    /**
     * @param $date
     * @param $hour
     * @param $username
     * @param $userEmail
     *
     * @return void
     */
    public function send($date, $hour, $username, $userEmail)
    {
        $emailToSend = $this->dataHelper->getEmailsToSendLoginActivityInformation();
        $emailTemplate = $this->dataHelper->getEmailTemplateForLoginActivity();

        $templateObject = new DataObject();
        $templateObject->setData('loginDate', $date);
        $templateObject->setData('loginHour', $hour);
        $templateObject->setData('username', $username);
        $templateObject->setData('email', $userEmail);

        $emailToSendArray = explode(',', $emailToSend);
        foreach ($emailToSendArray as $email) {
            try {
                $email = trim($email);
                $this->validateEmail($email);

                $sender = $this->getSender();

                $this->sendEmail(
                    $emailTemplate,
                    $templateObject,
                    $sender,
                    $email
                );
            } catch (LocalizedException $e) {
                $this->logger->error(sprintf(
                    'Invalid email address found: %s | Login Date: %s | Login Hour: %s | User Email: %s | Username: %s',
                    $email,
                    $date,
                    $hour,
                    $userEmail,
                    $username
                ));
            }
        }
    }

    /**
     * @param string     $emailTemplate
     * @param DataObject $templateObject
     * @param array      $sender
     * @param string     $emailToSend
     *
     * @return void
     *
     * @throws LocalizedException
     */
    private function sendEmail(
        string $emailTemplate,
        DataObject $templateObject,
        array $sender,
        string $emailToSend
    ) {
        $transport = $this->transportBuilder
            ->setTemplateIdentifier($emailTemplate)
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars(['data' => $templateObject])
            ->setFrom($sender);

        $transport->addTo($emailToSend);
        $transport = $transport->getTransport();
        $transport->sendMessage();
    }

    /**
     *
     * @param string $email
     * @throws LocalizedException
     */
    private function validateEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new LocalizedException(__("Invalid email address: %1", $email));
        }
    }

    /**
     *
     * @return array
     */
    private function getSender(): array
    {
        return [
            'name' => $this->scopeConfig->getValue(
                'trans_email/ident_general/name',
                ScopeInterface::SCOPE_STORE
            ),
            'email' => $this->scopeConfig->getValue(
                'trans_email/ident_general/email',
                ScopeInterface::SCOPE_STORE
            ),
        ];
    }
}
