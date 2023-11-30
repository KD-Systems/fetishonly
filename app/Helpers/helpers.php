<?php

use App\Providers\GenericHelperServiceProvider;
use App\Providers\InstallerServiceProvider;
use App\TwitterAccess;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

if (! function_exists('getSetting')) {
    function getSetting($key, $default = null)
    {
        try {
            $dbSetting = TCG\Voyager\Facades\Voyager::setting($key, $default);
        }
        catch (Exception $exception){
            $dbSetting = null;
        }

        $configSetting = config('app.'.$key);
        if ($dbSetting) {
            // If voyager setting is file type, extract the value only
            if (is_string($dbSetting) && strpos($dbSetting, 'download_link')) {
                $file = json_decode($dbSetting);
                if ($file) {
                    $file = Storage::disk(config('filesystems.defaultFilesystemDriver'))->url(str_replace('\\','/',$file[0]->download_link));
                }
                return $file;
            }

            return $dbSetting;
        }
        if ($configSetting) {
            return $configSetting;
        }

        return $default;
    }
}

function getLockCode(){
    if(session()->get(InstallerServiceProvider::$lockCode) == env('APP_KEY')){
        return true;
    }
    else{
        return false;
    }
}

function setLockCode($code){
    $sessData = [];
    $sessData[$code] = env('APP_KEY');
    session($sessData);
    return true;
}

function getUserAvatarAttribute($a){
    return GenericHelperServiceProvider::getStorageAvatarPath($a);
}

function getLicenseType(){
    $licenseType = 'Unlicensed';
    if(file_exists(storage_path('app/installed'))){
        $licenseV = json_decode(file_get_contents(storage_path('app/installed')));
        if(isset($licenseV->data) && isset($licenseV->data->license)){
            $licenseType = $licenseV->data->license;
        }
    }
    return $licenseType;
}

function handledExec($command, $throw_exception = true) {
    $result = exec('('.$command.')', $output, $return_code);
    if ($throw_exception) {
        if (($result === false) || ($return_code !== 0)) {
            throw new Exception('Error processing command: ' . $command . "\n\n" . implode("\n", $output) . "\n\n");
        }
    }
    return implode("\n", $output);
}

function checkMysqlndForPDO(){
    return true;
    $dbHost = env('DB_HOST');
    $dbUser = env('DB_USERNAME');
    $dbPass = env('DB_PASSWORD');
    $dbName = env('DB_DATABASE');

    $pdo = new PDO('mysql:host=' . $dbHost . ';dbname=' . $dbName, $dbUser, $dbPass);
    if (strpos($pdo->getAttribute(PDO::ATTR_CLIENT_VERSION), 'mysqlnd') !== false) {
        return true;
    }
    return false;
}

function checkForMysqlND(){
    if (extension_loaded('mysqlnd')) {
        return true;
    }
    return false;
}

function getTwitterToken(TwitterAccess $twitterAccess) {

    if($twitterAccess->refreshed_at > now()->subHours(2))
    {
        return $twitterAccess;
    }

    $client_id = env('X_CLIENT_ID', '');
    $client_secret = env('X_CLIENT_SECRET', '');
    $basic_auth = base64_encode($client_id.':'.$client_secret);

    $client = new Client();

    try {
        $response = $client->post('https://api.twitter.com/2/oauth2/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic '.$basic_auth
            ],
            'form_params' => [
                'refresh_token' => $twitterAccess->refresh_token,
                'grant_type'    => 'refresh_token',
                'client_id'     => $client_id,
                'code_verifier' => 'challenge'
            ]
        ]);

        if($response->getStatusCode() != 200)
            throw new Exception('Error');

        $response = json_decode($response->getBody()->getContents(), true);

        $twitterAccess->update([
            'access_token'  => $response['access_token'],
            'refresh_token' => $response['refresh_token'],
            'refreshed_at'  => now()
        ]);

        return $twitterAccess;

    } catch (Exception $ex) {
        return $ex->getMessage();
    }

}
