<?php

use Carbon\Carbon;
use App\Lib\Captcha;
use App\Notify\Notify;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\Frontend;
use App\Constants\Status;
use App\Models\Extension;
use App\Models\Commission;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\GeneralSetting;
use App\Lib\GoogleAuthenticator;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

use App\Models\CryptoCurrency;
use App\Models\Manipulation;

function systemDetails()
{
    $system['name']          = 'tradelab';
    $system['version']       = '2.0';
    $system['build_version'] = '4.4.1';
    return $system;
}

function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function activeTemplate($asset = false)
{
    $general = gs();
    $template = $general->active_template;
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $general = gs();
    $template = $general->active_template;
    return $template;
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}


function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}


function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/templates/' . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


function getImage($image, $size = null)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}


function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true)
{
    $general = gs();
    $globalShortCodes = [
        'site_name' => $general->site_name,
        'site_currency' => $general->cur_text,
        'currency_symbol' => $general->cur_sym,
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->userColumn = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}


function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3) $class = 'side-menu--open';
    elseif ($type == 2) $class = 'sidebar-submenu__open';
    else $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param)) return $class;
            else return;
        }
        return $class;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}


function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}


function gatewayRedirectUrl($type = false)
{
    if ($type) {
        return 'user.deposit.history';
    } else {
        return 'user.deposit.index';
    }
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function gs()
{
    $general = GeneralSetting::first();
    /*
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    */
    return $general;
}

function getCoinRate($coinId)
{
    // 1. Fetch real crypto rate from API
    $url    = 'https://min-api.cryptocompare.com/data/price?fsym=' . $coinId . '&tsyms=USD';
    $crypto = @file_get_contents($url); // Suppress warning if API fails
    $usd    = json_decode($crypto, true);

    if (!is_array($usd) || !isset($usd['USD'])) {
        \Log::error('CryptoCompare API error or invalid response.', ['response' => $usd, 'url' => $url]);
        return null; // Or handle gracefully as per your needs
    }

    $cryptoRate = $usd['USD'];

    // 2. Get current time
    $now = Carbon::now();

    // 3. Find crypto record by symbol
    $cryptoModel = CryptoCurrency::select('id')->where('symbol', $coinId)->first();
    if (!$cryptoModel) {
        \Log::warning('CryptoCurrency not found for symbol', ['symbol' => $coinId]);
        return $cryptoRate; // Return real rate if crypto is not found in DB
    }

    // 4. Find active manipulation for this crypto and time window
    $manipulation = Manipulation::select('id', 'start_time', 'end_time', 'min', 'max', 'prediction_override')
        ->where('crypto_id', $cryptoModel->id)
        ->where('start_time', '<=', $now)
        ->where('end_time', '>=', $now)
        ->first();

    // 5. If manipulation is active, smoothly adjust the value
    if ($manipulation) {
        $startTime = Carbon::parse($manipulation->start_time);
        $endTime   = Carbon::parse($manipulation->end_time);
        $min       = floatval($manipulation->min);
        $max       = floatval($manipulation->max);

        // Avoid division by zero
        $totalSeconds   = max(1, $endTime->diffInSeconds($startTime));
        $elapsedSeconds = max(0, $now->diffInSeconds($startTime));
        $progress       = min(max($elapsedSeconds / $totalSeconds, 0), 1); // Clamp 0-1

        // Linear interpolation for adjustment
        $currentAdjustment = $min + ($max - $min) * $progress;

        if ($manipulation->prediction_override == 1) { // High
            $cryptoRate += $currentAdjustment;
        } elseif ($manipulation->prediction_override == 2) { // Low
            $cryptoRate = max(0, $cryptoRate - $currentAdjustment);
        }

        \Log::info('Current adjustment:', [
            'cryptoModel' => $cryptoModel->id,
            'manipulation' => $manipulation->id,
            'currentAdjustment' => $currentAdjustment,
            'progress' => $progress,
            'min' => $min,
            'max' => $max,
            'cryptoRate' => $cryptoRate,
        ]);
    }

    return $cryptoRate;
}

function referralCommission($user, $amount)
{
    $referBy = User::where('id', $user->ref_by)->first();
    if ($referBy) {
        $gs    = gs();
        $bonus = (($amount * $gs->referral_bonus) / 100);

        if ($bonus > 0) {

            $referBy->balance += $bonus;
            $referBy->save();

            $details = "Commission From " . $user->username . " for Deposit.";

            $commission               = new Commission();
            $commission->user_id      = $referBy->id;
            $commission->from_user_id = $user->id;
            $commission->amount       = $bonus;
            $commission->details      = $details;
            $commission->post_balance = $referBy->balance;
            $commission->trx          = getTrx();
            $commission->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $referBy->id;
            $transaction->amount       = $bonus;
            $transaction->post_balance = getAmount($referBy->balance);
            $transaction->trx_type     = '+';
            $transaction->details      = $details;
            $transaction->trx          = getTrx();
            $transaction->save();

            notify($referBy, 'COMMISSION_BONUS',[
                'amount'       => $commission->amount,
                'main_balance' => $referBy->balance,
                'trx'          => $commission->trx,
                'full_name'    => $user->full_name
            ]);
        }
    }
}
