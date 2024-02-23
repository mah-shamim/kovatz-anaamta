<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 *
 * Modified by woocommerce on 24-October-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Automattic\WooCommerce\Bookings\Vendor\Google\Service\Calendar;

class CalendarListEntryNotificationSettings extends \Automattic\WooCommerce\Bookings\Vendor\Google\Collection
{
  protected $collection_key = 'notifications';
  protected $notificationsType = CalendarNotification::class;
  protected $notificationsDataType = 'array';

  /**
   * @param CalendarNotification[]
   */
  public function setNotifications($notifications)
  {
    $this->notifications = $notifications;
  }
  /**
   * @return CalendarNotification[]
   */
  public function getNotifications()
  {
    return $this->notifications;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CalendarListEntryNotificationSettings::class, 'Google_Service_Calendar_CalendarListEntryNotificationSettings');
