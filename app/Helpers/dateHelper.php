<?php
function convertToUTC(string $date, string $time, string $timezone): string
{
    $dt = new DateTime(
        "$date $time",
        new DateTimeZone($timezone)
    );

    $dt->setTimezone(new DateTimeZone('UTC'));
    return $dt->format('Y-m-d H:i:s');
}
function convertFromUTC(string $utcDateTime, string $timezone): string
{
    $dt = new DateTime($utcDateTime, new DateTimeZone('UTC'));
    $dt->setTimezone(new DateTimeZone($timezone));
    return $dt->format('Y-m-d H:i:s');
}
  function toMinutes($time) {
        [$h, $m] = explode(':', $time);
        return ((int)$h * 60) + (int)$m;
    }

    function toTime($minutes) {
        return sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);
    }
