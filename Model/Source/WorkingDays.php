<?php
/**
 * @package KiwiCommerce\AdminActivity
 * @author  Stenik Magento Team <magedev@stenik.bg>
 */

declare(strict_types=1);

namespace KiwiCommerce\AdminActivity\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class WorkingDays
 */
class WorkingDays implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'понеделник', 'label' => __('Понеделник')],
            ['value' => 'вторник', 'label' => __('Вторник')],
            ['value' => 'сряда', 'label' => __('Сряда')],
            ['value' => 'четвъртък', 'label' => __('Четвъртък')],
            ['value' => 'петък', 'label' => __('Петък')],
            ['value' => 'събота', 'label' => __('Събота')],
            ['value' => 'неделя', 'label' => __('Неделя')],
        ];
    }
}

