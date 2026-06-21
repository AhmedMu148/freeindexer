<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Indexer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IndexerController extends Controller
{
  private $proxiesCache = [];

  public function home()
  {
    return view('home');
  }

  public function processPage(Request $request)
  {

    Log::info('process Request', $request->all());

    $request->validate([
      'user_urls'      => 'nullable|string',
      'domain_url'     => 'nullable|string',
      'list-type'      => 'required|in:quick-list,full-list',
      'domain-indexer' => 'nullable|string',
    ]);

    // need to check if user not login can't user Full List
    if (
      $request->input('list-type') === 'full-list' &&
      !Auth::check()
    ) {
      // Log::info('log info', context: ['Back-0']);
      return back()->with('error', 'You must be logged in to use Full List option.');
    }

    $domainIndexer = $request->has('domain-indexer');
    $urlsText = $domainIndexer
      ? trim((string)$request->input('domain_url', ''))
      : trim((string)$request->input('user_urls', ''));
    if ($urlsText === '') {
      return back()->with('error', 'You must enter at least 1 URL');
    }
    $urls = $domainIndexer
      ? [rtrim($urlsText, '/')] // Domain mode = 1 item
      : $this->normalizeUrlsText($urlsText);
    if (count($urls) < 1) {
      return back()->with('error', 'You must enter at least 1 URL');
    }
    if (count($urls) > 5000) {
      return back()->with('error', 'You can submit a maximum of 5000 URLs at a time.');
    }

    // $domainIndexer  = $request->has('domain-indexer');
    // $urlsText       = $domainIndexer ? trim((string)$request->input('domain_url'))
    //   : trim((string)$request->input('user_urls'));

    // if ($urlsText === '') {
    //   Log::info('log info', ['Back-1']);
    //   return back()->with('error', 'You must enter at least 1 URL');
    // }

    // maxminm urlsText 5000 links 
    // if (count(preg_split('/\r\n|\r|\n/', $urlsText)) > 5000) {
    //   return back()->with('error', 'You can submit a maximum of 5000 URLs at a time.');
    // }

    // $files_list       = DB::table('files_list')->where('status', 1)->get()->keyBy('type_id');

    $files_list       = DB::table('files_list')->where('status', 1)->get()->keyBy('type_id');
    $domainFullFile   = $files_list->get(3)->path ?? null;
    $deepFullFile     = $files_list->get(1)->path ?? null;
    $domainQuickFile  = $files_list->get(4)->path ?? null;
    $deepQuickFile    = $files_list->get(2)->path ?? null;

    // $files = DB::table('files_list')
    //   ->where('status', 1)
    //   ->whereIn('type_id', [1, 2, 3, 4])
    //   ->pluck('path', 'type_id');  // Collection: [type_id => path]

    // $domainFullFile  = $files->get(3);
    // $deepFullFile    = $files->get(1);
    // $domainQuickFile = $files->get(4);
    // $deepQuickFile   = $files->get(2);

    if (in_array(null, [$domainFullFile, $deepFullFile, $domainQuickFile, $deepQuickFile], true)) {
      Log::info('log info', ['Back-2']);
      return back()->with(
        'error',
        'Missing files_list config for one or more type_id (1,2,3,4) with status=1.'
      );
    }

    session([
      'indexer' => [
        'urlsText'        => $urlsText,
        'listType'        => $request->input('list-type'),
        'isDomain'        => $domainIndexer,
        'domainFullFile'  => $domainFullFile,
        'deepFullFile'    => $deepFullFile,
        'domainQuickFile' => $domainQuickFile,
        'deepQuickFile'   => $deepQuickFile,
      ],
    ]);

    return view('indexer_process', [
      'urlsText'          => $urlsText,
      'listType'          => $request->input('list-type'),
      'isDomain'          => $domainIndexer,
      'domainFullFile'    => $domainFullFile,
      'deepFullFile'      => $deepFullFile,
      'domainQuickFile'   => $domainQuickFile,
      'deepQuickFile'     => $deepQuickFile,
    ]);
  }

  private function normalizeUrlsText(string $urlsText): array
  {
    $lines = preg_split('/\r\n|\r|\n/', $urlsText);
    $urls = array_values(array_filter(array_map(function ($u) {
      $u = trim((string)$u);
      if ($u === '') return null;
      return rtrim($u, '/');
    }, $lines)));

    return $urls;
  }

  public function submit(Request $request)
  {

    $user = Auth::user();
    $uid  = $user ? $user['id'] : 0;

    Log::info('TEMPLATE_DEBUG', $request->all());

    // dd($request);

    $urlsText = trim((string)$request->input('urls_text', ''));
    $template = $request->input('template'); // lists/...txt

    if ($urlsText === '' || !$template) {
      return response()->json(['status' => 'error', 'message' => 'Missing data'], 422);
    }
    if (!Storage::disk('public')->exists($template)) {
      return response()->json(['status' => 'error', 'message' => "Template not found: $template"], 500);
    }

    // $urls = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $urlsText))));

    $urls = preg_split('/\r\n|\r|\n/', $urlsText);

    // Clean: trim + remove empty lines + remove trailing /
    $urls = array_values(array_filter(array_map(function ($u) {
      $u = trim((string)$u);
      if ($u === '') {
        return null;
      }
      // Remove any / at the end of the link
      $u = rtrim($u, '/');
      return $u;
    }, $urls)));

    if (!count($urls)) {
      return response()->json(['status' => 'error', 'message' => 'No valid URLs'], 422);
    }

    $hash             = Str::random(16);
    $warehouse        = "warehouse_secure/{$hash}.txt";
    $warehouse_to_s3  = "warehouse_to_s3/{$hash}.txt";
    $generated        = "tmp/{$hash}.txt";

    Storage::put($warehouse, implode(PHP_EOL, $urls));
    Storage::put($warehouse_to_s3, implode(PHP_EOL, $urls));

    $firstUserUrl = $this->popFirstLine($warehouse);
    if (!$firstUserUrl) {
      return response()->json(['status' => 'error', 'message' => 'Failed: No URLs'], 422);
    }

    $this->generateFromTemplate($firstUserUrl, $template, $generated);
    $perUrlCount   = $this->countLines($generated);
    $totalExpected = $perUrlCount * count($urls);

    $urlsLen = count($urls);

    // Submit order
    if ($uid != 0) {

      // 1) Prevent inactive user
      $isVerified = DB::table('users')
        ->where('id', $uid)
        ->whereNotNull('email_verified_at')
        ->exists();

      if (! $isVerified) {
        return response()->json([
          'status'  => 'error',
          'message' => 'Please verify your email address before using this feature.',
        ], 403);
      }

      $indexer_points_data = DB::table('indexer_points')->where('uid', $uid)->first();
      $indexer_points      = $indexer_points_data ? $indexer_points_data->points : 0;
      $indexer_used        = $indexer_points_data ? $indexer_points_data->used : 0;

      if ($urlsLen + $indexer_used > $indexer_points) {
        return response()->json([
          'status'  => 'error',
          'message' => "You cannot process more than {$indexer_points} URLs. To process more please upgrade.",
        ], 422);
      }
      $orderData = json_encode($urls);
      Indexer::create([
        'uid'         => $uid,
        'date'        => $orderData,
        'urls_count'  => $urlsLen,
        'status_id'   => '1',
      ]);

      // Update user uses
      DB::table('indexer_points')->where('uid', $uid)->update([
        'used' => $urlsLen + $indexer_used,
      ]);
    }

    // Submit order
    if ($uid == 0) {
      $ip                       = $request->ip();
      $ip_addresses_points_data = DB::table('ip_addresses')->where('ip', $ip)->first();
      $ip_addresses_points      = $ip_addresses_points_data ? $ip_addresses_points_data->points : 0;
      $ip_addresses_used        = $ip_addresses_points_data ? $ip_addresses_points_data->used : 0;

      if ($urlsLen + $ip_addresses_used > $ip_addresses_points) {
        return response()->json([
          'status'  => 'error',
          'message' => "You cannot process more than {$ip_addresses_points} URLs. To process more please upgrade.",
        ], 422);
      }

      // Update user uses
      // DB::table('ip_addresses')->where('ip', $ip)->update([
      //   'used' => $urlsLen + $ip_addresses_used,
      // ]);

      DB::table('ip_addresses')->where('ip', $ip)->update([
        'used'       => $urlsLen + $ip_addresses_used,
        'updated_at' => now(),
      ]);
    }

    return response()->json([
      'status'         => 'start',
      'hash'           => $hash,
      'total_expected' => $totalExpected,
    ]);
  }

  public function step(Request $request)
  {

    // dd(config('cache.default'));

    $hash     = $request->input('hash');
    $template = $request->input('template');

    // sleep(0.5) get time from db ;
    // $delayRow = DB::table('config_indexer_engen')->where('id', 1)->first();
    // $delayMicroseconds = $delayRow ? (int)$delayRow->indexer_sleep : 500000;
    // usleep($delayMicroseconds);
    // $delayMicroseconds = $this->getIndexerSleep();
    // usleep($delayMicroseconds);

    // $delayMicroseconds = config('indexer.sleep');
    // usleep($delayMicroseconds);

    if (!$hash || !$template) return response('f::Missing parameters', 422);

    // // start new code
    // cache cate: 200ms per job hash with job hash
    // $minIntervalMs = 200;
    $minIntervalMs  = 100;
    $gateKey        = "indexer:last_step_at:{$hash}";
    $nowMs          = (int) floor(microtime(true) * 1000);
    $lastMs = Cache::get($gateKey);
    if ($lastMs !== null && ($nowMs - (int)$lastMs) < $minIntervalMs) {
      $wait = $minIntervalMs - ($nowMs - (int)$lastMs);
      return response()->json([
        'status' => 'wait',
        'retry_after_ms' => max(20, $wait),
      ], 200);
    }
    Cache::put($gateKey, $nowMs, now()->addMinutes(30));
    // // end new code

    $generated = "tmp/{$hash}.txt";
    $warehouse = "warehouse_secure/{$hash}.txt";

    if (!Storage::exists($generated) || !Storage::exists($warehouse)) {
      return response('f::File not exists', 200);
    }

    $link = $this->popLastLine($generated);
    if (!$link) {
      if ($this->isEmpty($warehouse)) {

        Storage::delete($generated);
        Storage::delete($warehouse);

        // new code gate delete
        Cache::forget("indexer:last_step_at:{$hash}");

        return response("d:<STRONG>Done</STRONG>", 200);
      }
      $userUrl = $this->popFirstLine($warehouse);
      if (!$userUrl) return response("d:<STRONG>Done</STRONG>", 200);

      $this->generateFromTemplate($userUrl, $template, $generated);
      $link = $this->popLastLine($generated);
      if (!$link) return response("d:<STRONG>Done</STRONG>", 200);
    }

    $url = trim($link);
    $ok  = $this->ping($url);

    if ($ok) {
      return response("<span class='text-success col-md-1 py-0'>Success:</span> <span class='col-md-6 text-nowrap'>{$url}</span>", 200);
    }
    return response("<span class='text-warning col-md-3'>Failed:</span> <span class='col-md-6 text-nowrap'>{$url}</span>", 200);
  }

  public function processReload(Request $request)
  {
    $data = $request->session()->get('indexer');

    if (!$data) {
      return redirect()->route('/')->with('error', 'Session expired, please submit URLs again.');
    }

    return view('indexer_process', [
      'urlsText'        => $data['urlsText'],
      'listType'        => $data['listType'],
      'isDomain'        => $data['isDomain'],
      'domainFullFile'  => $data['domainFullFile'],
      'deepFullFile'    => $data['deepFullFile'],
      'domainQuickFile' => $data['domainQuickFile'],
      'deepQuickFile'   => $data['deepQuickFile'],
    ]);
  }

  private function popFirstLine(string $path): ?string
  {
    if (!Storage::exists($path)) return null;
    $content = Storage::get($path);
    if ($content === '') return null;
    $lines = preg_split('/\r\n|\r|\n/', $content);
    $first = array_shift($lines);
    Storage::put($path, implode(PHP_EOL, $lines));
    $lines = null; // free memory
    if ($first === null) return null;

    // $url = trim($first);
    // $url = preg_replace('#^https?://#i', '', $url);
    // $url = rtrim($url, '/');

    // return $url;
    return trim(str_replace(['http://', 'https://'], '', $first));
  }

  private function popLastLine(string $path): ?string
  {

    $disk = Storage::disk('local');

    // Check if file exists on that disk
    if (!$disk->exists($path)) {
      return null;
    }

    // Get the absolute filesystem path
    $fullPath = $disk->path($path);

    // Open file in read/write mode without truncating
    $handle = fopen($fullPath, 'c+');
    if (!$handle) {
      return null;
    }

    $stat = fstat($handle);
    $size = $stat['size'];

    // Empty file → nothing to return
    if ($size === 0) {
      fclose($handle);
      return null;
    }

    $pos = $size - 1;
    $lastLine = '';

    // 1) Skip trailing newlines at the end of the file
    while ($pos >= 0) {
      fseek($handle, $pos);
      $char = fgetc($handle);

      if ($char !== "\n" && $char !== "\r") {
        break;
      }

      $pos--;
    }

    // If file has only one line with no newline characters
    if ($pos < 0) {
      fseek($handle, 0);
      $lastLine = stream_get_contents($handle);

      // Truncate entire file
      ftruncate($handle, 0);
      fclose($handle);

      return trim(str_replace(['http://', 'https://'], '', $lastLine));
    }

    // endPos = last character of the last line
    $endPos = $pos;

    // 2) Move backward to find the beginning of the last line
    while ($pos >= 0) {
      fseek($handle, $pos);
      $char = fgetc($handle);

      if ($char === "\n") {
        $pos++;
        break;
      }

      $pos--;
    }

    // If no newline found, start from beginning of file
    if ($pos < 0) {
      $pos = 0;
    }

    // 3) Read last line
    $length = $endPos - $pos + 1;
    fseek($handle, $pos);
    $lastLine = fread($handle, $length);

    // 4) Truncate file to remove the last line
    ftruncate($handle, $pos);
    fclose($handle);

    return trim(str_replace(['http://', 'https://'], '', $lastLine));
  }

  private function generateFromTemplate(string $userDomain, string $templatePath, string $outPath): void
  {
    $tpl = Storage::disk('public')->get($templatePath);
    Storage::put($outPath, str_replace('yourdomain.com', $userDomain, $tpl));
  }

  private function countLines(string $path): int
  {
    if (!Storage::exists($path)) return 0;
    $c = trim(Storage::get($path));
    return $c === '' ? 0 : count(preg_split('/\r\n|\r|\n/', $c));
  }

  private function isEmpty(string $path): bool
  {
    return !Storage::exists($path) || Storage::size($path) === 0;
  }

  private function ping(string $url): bool
  {
    if (!preg_match('#^https?://#i', $url)) {
      $url = 'http://' . $url;
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
      CURLOPT_URL            => $url,
      CURLOPT_USERAGENT      => "Mozilla/5.0",
      CURLOPT_REFERER        => $url,
      CURLOPT_HEADER         => 0,   // no headers in output
      CURLOPT_NOBODY         => 1,   // HEAD request only, much lighter
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_CONNECTTIMEOUT => 3,
      CURLOPT_TIMEOUT        => 4,
    ]);

    // $ch = curl_init();
    // curl_setopt_array($ch, [
    //   CURLOPT_URL            => $url,
    //   CURLOPT_USERAGENT      => "Mozilla/5.0",
    //   CURLOPT_REFERER        => $url,
    //   CURLOPT_HEADER         => 1,
    //   CURLOPT_RETURNTRANSFER => 1,
    //   CURLOPT_CONNECTTIMEOUT => 3,
    //   CURLOPT_TIMEOUT        => 4,
    // ]);

    curl_exec($ch);
    $ok = !curl_errno($ch);
    curl_close($ch);
    return $ok;
  }

  // private function ping(string $url): bool
  // {

  //   if (!preg_match('#^https?://#i', $url)) {
  //     $url = 'http://' . $url;
  //   }

  //   // function inside to do curl with or without proxy
  //   $doRequest = function (?array $proxy) use ($url) {
  //     $ch = curl_init();

  //     $options = [
  //       CURLOPT_URL            => $url,
  //       CURLOPT_USERAGENT      => "Mozilla/5.0",
  //       CURLOPT_REFERER        => $url,
  //       CURLOPT_HEADER         => 1,
  //       CURLOPT_RETURNTRANSFER => 1,
  //       CURLOPT_CONNECTTIMEOUT => 6,
  //       CURLOPT_TIMEOUT        => 6,
  //     ];

  //     // if ($proxy) {
  //     //   $options[CURLOPT_PROXY]        = $proxy['host'] . ':' . $proxy['port'];
  //     //   $options[CURLOPT_PROXYTYPE]    = CURLPROXY_HTTP; // proxy HTTP
  //     //   $options[CURLOPT_PROXYUSERPWD] = $proxy['user'] . ':' . $proxy['pass'];
  //     // }

  //     if ($proxy) {
  //       $options[CURLOPT_PROXY]     = $proxy['host'] . ':' . $proxy['port'];
  //       $options[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP; // HTTP proxy
  //       if (!empty($proxy['user']) && !empty($proxy['pass'])) {
  //         $options[CURLOPT_PROXYUSERPWD] = $proxy['user'] . ':' . $proxy['pass'];
  //       }
  //     }

  //     curl_setopt_array($ch, $options);
  //     curl_exec($ch);

  //     $errno = curl_errno($ch);
  //     curl_close($ch);

  //     return $errno === 0;
  //   };

  //   // Try with proxy first
  //   $proxy = $this->getRandomProxy();
  //   if ($proxy) {
  //     $ok = $doRequest($proxy);
  //     if ($ok) {
  //       // Log::info("PING USING PROXY", [
  //       //   'url'   => $url,
  //       //   'proxy' => $proxy,
  //       // ]);
  //       return true;
  //     } else {
  //       // Log::warning("PROXY FAILED → switching to direct", [
  //       //   'url'   => $url,
  //       //   'proxy' => $proxy,
  //       // ]);
  //     }
  //   }

  //   // 2) Try direct without proxy
  //   $ok = $doRequest(null);

  //   if ($ok) {
  //     // Log::info("PING DIRECT", ['url' => $url]);
  //   } else {
  //     // Log::warning("PING DIRECT FAILED", ['url' => $url]);
  //   }

  //   return $ok;
  //   // return $doRequest(null);
  // }

  private function loadProxiesFromLatestFile(): array
  {
    if (!empty($this->proxiesCache)) {
      return $this->proxiesCache;
    }
    $row = DB::table('proxy_files')
      ->orderBy('created_at', 'desc')
      ->first();
    if (!$row || empty($row->file_path)) {
      return $this->proxiesCache = [];
    }
    $path = $row->file_path;
    // https://freeindexer.com/storage/app/public/proxy-list/20251116141058.txt
    if (preg_match('#^https?://#i', $path)) {
      $parsedPath = parse_url($path, PHP_URL_PATH) ?? '';
      $parsedPath = ltrim($parsedPath, '/');
      $prefix = 'storage/app/public/';
      if (strpos($parsedPath, $prefix) === 0) {
        $parsedPath = substr($parsedPath, strlen($prefix));
      }
      $path = $parsedPath;
    }
    if (!Storage::disk('public')->exists($path)) {
      Log::warning('Proxy file not found in storage', ['path' => $path]);
      return $this->proxiesCache = [];
    }
    $content = Storage::disk('public')->get($path);
    $lines   = preg_split('/\r\n|\r|\n/', $content);
    $proxies = [];
    foreach ($lines as $line) {
      $line = trim((string)$line);
      if ($line === '') continue;
      $proxies[] = $line;
    }
    return $this->proxiesCache = $proxies;
  }

  private function getRandomProxy(): ?array
  {
    $proxies = $this->loadProxiesFromLatestFile();
    if (empty($proxies)) {
      return null;
    }
    $raw = $proxies[array_rand($proxies)];
    $parts = explode(':', $raw);
    if (count($parts) < 2) {
      return null;
    }
    return [
      'host' => $parts[0],
      'port' => $parts[1],
      'user' => $parts[2] ?? null,
      'pass' => $parts[3] ?? null,
    ];
  }
}
