<?php
// news.php — robust RSS→JSON aggregator with cURL + file cache + diagnostics
// Output: JSON array of items: {source, sourceLink, title, url, published, summary, image}

header('Content-Type: application/json; charset=UTF-8');

// 1) Configure your feeds here
$FEEDS = [
  'https://www.themeparkinsider.com/news/rss.xml',
  'https://www.coaster101.com/feed',
  // Add more feeds (many WordPress sites expose /feed)
];

// 2) Cache setup
$TTL = 15 * 60; // seconds
$cacheDir = sys_get_temp_dir(); // writable on most hosts
$CACHE_FILE = rtrim($cacheDir, '/\\') . '/news-cache.json';

// 3) Simple logger (into temp dir)
$LOG_FILE = rtrim($cacheDir, '/\\') . '/news-log.txt';
function log_msg($m) {
  global $LOG_FILE;
  @file_put_contents($LOG_FILE, '['.date('c')."] ".$m."\n", FILE_APPEND);
}

// 4) Optional diagnostics mode: /news.php?test=1
$DIAG = isset($_GET['test']) && $_GET['test'] == '1';

// Serve cache if fresh
if (!$DIAG && file_exists($CACHE_FILE) && (time() - filemtime($CACHE_FILE) < $TTL)) {
  readfile($CACHE_FILE);
  exit;
}

function http_get($url) {
  // Prefer cURL
  if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_CONNECTTIMEOUT => 8,
      CURLOPT_TIMEOUT => 12,
      CURLOPT_USERAGENT => 'PhoenixNewsBot/1.0 (+contact@yourdomain)',
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_ENCODING => '',
    ]);
    $body = curl_exec($ch);
    $err  = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    if ($err) throw new Exception("cURL error: $err");
    if ($code >= 400) throw new Exception("HTTP $code from $url");
    return $body;
  }
  // Fallback to file_get_contents if allowed
  $ctx = stream_context_create([
    'http' => [
      'method' => 'GET',
      'header' => "User-Agent: PhoenixNewsBot/1.0\r\n",
      'timeout' => 12
    ]
  ]);
  $body = @file_get_contents($url, false, $ctx);
  if ($body === false) throw new Exception("file_get_contents failed for $url");
  return $body;
}

function extract_image_from_array($arr) {
  // enclosure url
  if (isset($arr['enclosure']['@attributes']['url'])) return $arr['enclosure']['@attributes']['url'];
  // media:content variations
  if (isset($arr['media:content']['@attributes']['url'])) return $arr['media:content']['@attributes']['url'];
  if (isset($arr['media']['content']['@attributes']['url'])) return $arr['media']['content']['@attributes']['url'];
  // find first <img src="...">
  $html = $arr['content:encoded'] ?? $arr['content'] ?? $arr['description'] ?? '';
  if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $m)) return $m[1];
  return null;
}

$items = [];
$errors = [];

foreach ($FEEDS as $url) {
  try {
    $raw = http_get($url);
    // Try SimpleXML first
    $xml = @simplexml_load_string($raw, 'SimpleXMLElement', LIBXML_NOCDATA);
    if (!$xml) {
      // Try DOMDocument fallback (some feeds are finicky)
      $dom = new DOMDocument();
      @$dom->loadXML($raw, LIBXML_NOERROR | LIBXML_NOWARNING);
      if ($dom->documentElement === null) throw new Exception('XML parse failed');
      $xml = simplexml_import_dom($dom);
    }

    // RSS 2.0
    if (isset($xml->channel->item)) {
      $source = (string)($xml->channel->title ?? parse_url($url, PHP_URL_HOST));
      $sourceLink = (string)($xml->channel->link ?? $url);
      foreach ($xml->channel->item as $it) {
        $arr = json_decode(json_encode($it), true);
        $items[] = [
          'source'     => $source,
          'sourceLink' => $sourceLink,
          'title'      => (string)($it->title ?? 'Untitled'),
          'url'        => (string)($it->link ?? ''),
          'published'  => (string)($it->pubDate ?? ($it->children('http://purl.org/dc/elements/1.1/')->date ?? '')),
          'summary'    => trim(strip_tags((string)($it->description ?? ''))),
          'image'      => extract_image_from_array($arr),
        ];
      }
      continue;
    }

    // Atom
    if (isset($xml->entry)) {
      $feedTitle = (string)($xml->title ?? parse_url($url, PHP_URL_HOST));
      $feedLink = '';
      foreach ($xml->link as $lnk) {
        $rel = (string)$lnk['rel'];
        if ($rel === 'alternate' || $rel === '') $feedLink = (string)$lnk['href'];
      }
      foreach ($xml->entry as $it) {
        $link = '';
        foreach ($it->link as $lnk) {
          $rel = (string)$lnk['rel'];
          if ($rel === 'alternate' || $rel === '') $link = (string)$lnk['href'];
        }
        $arr = json_decode(json_encode($it), true);
        $content = (string)($it->content ?? $it->summary ?? '');
        $items[] = [
          'source'     => $feedTitle,
          'sourceLink' => $feedLink ?: $url,
          'title'      => (string)($it->title ?? 'Untitled'),
          'url'        => $link,
          'published'  => (string)($it->updated ?? $it->published ?? ''),
          'summary'    => trim(strip_tags($content)),
          'image'      => extract_image_from_array($arr),
        ];
      }
      continue;
    }

    // Unknown format
    throw new Exception('Unknown feed format for ' . $url);

  } catch (Throwable $e) {
    $msg = "Feed failed: $url — " . $e->getMessage();
    $errors[] = $msg;
    log_msg($msg);
  }
}

// Sort & cap
usort($items, function($a, $b) {
  return strtotime($b['published'] ?? 0) <=> strtotime($a['published'] ?? 0);
});
$items = array_values(array_filter($items, fn($x) => !empty($x['url'])));
$items = array_slice($items, 0, 100);

// Diagnostics mode: show details to help debugging
if ($DIAG) {
  echo json_encode([
    'ok' => true,
    'count' => count($items),
    'cache_file' => $CACHE_FILE,
    'log_file' => $LOG_FILE,
    'errors' => $errors,
    'sample' => array_slice($items, 0, 3),
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  exit;
}

// Save cache (best-effort)
$json = json_encode($items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@file_put_contents($CACHE_FILE, $json);

// Emit
echo $json;
