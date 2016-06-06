<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
namespace Module\Event\Api;

use Pi;
use Pi\Application\Api\AbstractApi;

/*
 * Pi::api('time', 'event')->makeTime();
 */

class Time extends AbstractApi
{
    public function makeTime()
    {
        switch (Pi::config('date_calendar')) {
            // Set for Iran time
            case 'persian':
                require_once Pi::path('module') . '/event/src/Api/pdate.php';

                $nextMonth = pdate('m') + 1;
                $nextTwoMonth = pdate('m') + 2;
                $year = pdate('Y');
                if ($nextMonth > 12) {
                    $nextMonth = 1;
                    $year = $year + 1;
                }
                if ($nextTwoMonth > 12) {
                    $nextTwoMonth = 1;
                    $year = $year + 1;
                }

                $time = array(
                    'expired' => pmktime(0, 0, 0, pdate('m', strtotime("-1 Saturday")), pdate('d', strtotime("-1 Saturday")), pdate('Y', strtotime("-1 Saturday"))),
                    'thisWeek' => pmktime(0, 0, 0, pdate('m', strtotime("-1 Saturday")), pdate('d', strtotime("-1 Saturday")), pdate('Y', strtotime("-1 Saturday"))),
                    'nextWeek' => pmktime(0, 0, 0, pdate('m', strtotime("+1 Saturday")), pdate('d', strtotime("+1 Saturday")), pdate('Y', strtotime("+1 Saturday"))),
                    'nextMonth' => pmktime(0, 0, 0, $nextMonth, 1, $year),
                    'nextTwoMonth' => pmktime(0, 0, 0, $nextTwoMonth, 1, $year),
                );
                break;

            default:
                $time = array(
                    'expired' => strtotime("-1 Monday"),
                    'thisWeek' => strtotime("-1 Monday"),
                    'nextWeek' => strtotime("+1 Monday"),
                    'nextMonth' => strtotime('first day of next month'),
                    'nextTwoMonth' => strtotime('first day of +2 month'),
                );
                break;
        }
        return $time;
    }
}