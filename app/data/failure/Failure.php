<?php

namespace data\failure;

class Failure extends \Exception {
  public string $userDisplayError = "";
  
  public function __construct(
    string $userDisplayError,
    string $message,
    \Exception $previous = null,
  ) {
    $code = 0;
    $this->userDisplayError = $userDisplayError;
    parent::__construct($message, $code, $previous);
  }
  
  public function __toString(): string {
    return "Failure: $this->message because of $this->message";
  }
  
  public function getUserErrorBox() {
    $debug_html = "";
    if (__debug__) {
      $debug_html = "<div class='debug'>
<pre>$this->message</pre></div>";
      $stack = $this->getTrace();
      $debug_html .= "<div class='debug'>";
      foreach ($stack as $item) {
        $debug_html .= $item['file'] . " line " . $item['line'] . "<br>";
      }
      $debug_html .= "</div>";
    }
    // use w3-css
    $html = <<<HTML
      <div class="w3-panel w3-red">
        <h3>Oops!</h3>
        <p>$this->userDisplayError</p>
        <hr>
        $debug_html
      </div>

HTML;
  
  }
}
  
