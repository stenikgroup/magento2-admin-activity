<?php
/**
 * @package KiwiCommerce\AdminActivity
 * @author  Stenik Magento Team <magedev@stenik.bg>
 */

declare(strict_types=1);

namespace KiwiCommerce\AdminActivity\Action;

use KiwiCommerce\AdminActivity\Helper\Data as DataHelper;
use IntlDateFormatter;
use DateTime;

/**
 * Class CheckIsAllowedLoggingHour
 */
class CheckIsAllowedLoggingHour
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * @param DataHelper $dataHelper
     */
    public function __construct(DataHelper $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        $locale = 'bg_BG';
        $formatter = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            null,
            IntlDateFormatter::GREGORIAN,
            'EEEE'
        );

        date_default_timezone_set('Europe/Sofia');
        $currentDayName = $formatter->format(new DateTime());

        $currentHour = strtotime(date('H:i'));

        $workingDays = explode(',', $this->dataHelper->getWorkingDays());
        if (empty($workingDays) || !in_array($currentDayName, $workingDays)) {
            return false;
        }

        $workingHourStart = strtotime($this->dataHelper->getWorkingHourStart());
        $workingHourEnd = strtotime($this->dataHelper->getWorkingHourEnd());

        if ($currentHour < $workingHourStart || $currentHour > $workingHourEnd) {
            return false;
        }

        return true;
    }
}
