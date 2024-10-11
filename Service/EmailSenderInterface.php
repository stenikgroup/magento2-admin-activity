<?php
/**
 * @package KiwiCommerce\AdminActivity
 * @author  Stenik Magento Team <magedev@stenik.bg>
 */

declare(strict_types=1);

/** @noinspection RedundantSuppression */
/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
/** @noinspection PhpFullyQualifiedNameUsageInspection */

namespace KiwiCommerce\AdminActivity\Service;

/**
 * Interface EmailSenderInterface
 */
interface EmailSenderInterface
{
    /**
     * @param $date
     * @param $hour
     * @param $username
     * @param $userEmail
     *
     * @return mixed
     */
    public function send($date, $hour, $username, $userEmail);
}

