<?php

namespace data\failure;

/**
 * A correctness problem is a problem in the provided user data.
 *
 * For Example: The user entered an invalid email address or
 * the user entered a password that is too short.
 */
class CorrectnessProblem {
  public string $userDisplayMessage = "";
  public string $debugMessage = "";
  
  public function __construct(
    string $userDisplayMessage,
    string $debugMessage,
  ) {
    $this->userDisplayMessage = $userDisplayMessage;
    $this->debugMessage = $debugMessage;
  }
  
  public function __toString(): string {
    return "CorrectnessProblem: $this->userDisplayMessage because of $this->debugMessage";
  }
}