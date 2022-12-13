<?php

/**
 * This file is used for testing.
 *
 * It empties the database and creates new data.
 * Then it queries the pages and checks for the correct logs.
 *
 */

include "app/lib.php";

function error($message) {
  # display message in red
  echo "\033[31m$message\033[0m";
  exit;
}

function diffLines(array $wanted, array $got): array {
  $notFound = [];
  $thereButNotWanted = [];
  foreach ($wanted as $key => $line) {
    if (!in_array($line, $got)) {
      $notFound[] = $line . " (not found: Line $key)";
    }
  }
  foreach ($got as $key => $line) {
    if (!in_array($line, $wanted)) {
      $thereButNotWanted[] = $line. " (not wanted: Line $key)";
    }
  }
  return array_merge($notFound, $thereButNotWanted);
}

function postRequest(string $url, array $data): void
{
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === false) {
        error("Error while sending post request to $url");
    }
}


