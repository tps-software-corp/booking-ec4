<?php

namespace Plugin\TPSBooking;

use Eccube\Common\EccubeNav;

class Nav implements EccubeNav
{
    /**
     * @return array
     */
    public static function getNav()
    {
        return [
            'tpsbooking' => [
                'name' => 'admin.tpsbooking.menu_title',
                'icon' => 'fa-calendar',
                'children' => [
                    'tpsbooking_index' => [
                        'name' => 'admin.tpsbooking.booking_list',
                        'url' => 'tpsbooking_admin_booking',
                    ],
                ],
            ]
        ];
    }
}
