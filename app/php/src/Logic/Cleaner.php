<?php

namespace App\Logic;

use DOMDocument;

class Cleaner {

  public function html ($html): string {
    libxml_use_internal_errors(true);

    $html = filter_var(trim(strval($html)), FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_BACKTICK);
    $html = '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8"></head><body>'.$html.'</body></html>';

    $dom = new DOMDocument();
    $dom->loadHTML($html);

    $scripts = $dom->getElementsByTagName('script');

    if (count($scripts)) {
      foreach (iterator_to_array($scripts) as $script) {
        $script->parentNode->removeChild($script);
      }
    }

    $cleanHtml = $dom->saveHTML($dom->getElementsByTagName('body')->item(0));
    $cleanHtml = preg_replace('#^<body>|</body>$#', '', $cleanHtml);

    return trim($cleanHtml);
  }
}