<?php

namespace Drupal\site_location_time\Service;

/**
 * Custom service to get date & time according to timezone requested.
 */
class CustomService {

  /**
   * Created function for get date & time from selected timezone.
   */
  public function getDateTime($datetime) {

    $date = new \DateTime("now", new \DateTimeZone($datetime));
    return $date->format('jS M Y - h:i A');
  }

}
