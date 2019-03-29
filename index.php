<?php
session_start();

function exception_handler($exception)
{
    global $twig;
    global $config;
    global $routers;
    echo $twig->render('error.twig', ['error' => $exception->getMessage(), 'config' => $config, 'routers' => $routers, 'active' => $_SESSION['router']['name']]);
}

if ($config['debug']) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    var_dump($_SESSION);
} else {
    set_exception_handler('exception_handler');
}

if (file_exists('config.php')) {
    require_once('config.php');
} else {
    throw new Exception('Configuration file not found');
}

if (file_exists('vendor/autoload.php')) {
    require_once('vendor/autoload.php');
} else {
    throw new Exception('Composer not found');
}

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

if (!isset($routers) || empty($routers)) {
    throw new Exception('No routers configured.');
}

if (!isset($_SESSION['router'])) {
    $_SESSION['router'] = $routers[0];
}
if (isset($_GET['router'])) {
    if (!empty($routers[$_GET['router']])) {
        $_SESSION['router'] = $routers[$_GET['router']];
    } else {
        throw new Exception('Invalid Router.');
    }
}

function validateip($ip)
{
    if (strpos($ip, '/') !== false) {
        $ip = explode('/', $ip);
        $address = $ip[0];
        $mask = $ip[1];
    } else {
        $address = $ip;
    }
    if (isset($mask) && !is_numeric($mask)) {
        return false;
    } elseif (@inet_pton($address) !== false) {
        return true;
    } else {
        return false;
    }
}

function ipversion($txt)
{
    return strpos($txt, ":") === false ? 4 : 6;
}

function fetchdata($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $data = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('Connection to remote router failed.');
    } else {
        curl_close($ch);
        return $data;
    }
}

/*
 * Base
 */
$request = array();
if (isset($_GET['site'])) {
    $request['site'] = $_GET['site'];
}


/*
 * Peering Summary
*/
if (!isset($_GET['site'])) {
    // Fetch data
    $summaryurl = 'http://' . $_SESSION['router']['host'] . ':' . $_SESSION['router']['port'] . '/neighbors';
    $result = fetchdata($summaryurl);

    // Decode json
    $data = json_decode($result, true);

    // Resort peers (asn = array key)
    $peers = array();
    foreach ($data as $peer => $parameter) {
        foreach ($parameter as $key => $value) {
            $peers[$parameter['remoteAs']][$peer][$key] = $value;
        }
    }

    // Sort by key (asn)
    ksort($peers);

    // Send output
    echo $twig->render('peers.twig', ['data' => $peers, 'config' => $config, 'routers' => $routers, 'active' => $_SESSION['router']['name']]);
}


/*
 * Looking Glass
 */
if (isset($_GET['site']) && $_GET['site'] == 'lg') {
    $data = array();

    // Errors
    $errors = array();

    if (isset($_POST) && !empty($_POST)) {

        // -> Command
        if (isset($_POST['command'])) {
            $request['command'] = trim($_POST['command']);

            if (empty($request['command'])) {
                $errors[] = 'command-empty';
            }
        } else {
            $errors[] = 'command-notset';
        }

        // -> Argument
        if (isset($_POST['argument'])) {
            $request['argument'] = trim($_POST['argument']);

            if (empty($request['argument'])) {
                $errors[] = 'argument-empty';
            } elseif (!validateip($request['argument'])) {
                $errors[] = 'argument-invalid';
            }
        } else {
            $erorrs[] = 'argument-notset';
        }

        // -> Address family
        if (isset($request['command']) && isset($request['argument']) && !in_array('argument-invalid', $errors)) {
            if (ipversion($request['argument']) == '4' && $request['command'] == 'shbgpipv6') {
                $errors[] = 'afi-ipv6-prefix-ipv4';
            } elseif (ipversion($request['argument']) == '6' && $request['command'] == 'shbgpipv4') {
                $errors[] = 'afi-ipv4-prefix-ipv6';
            }
        }

        // Load LG
        if (empty($errors)) {
            switch ($request['command']) {
                case 'shbgpipv4':
                    $url = 'http://' . $_SESSION['router']['host'] . ':' . $_SESSION['router']['port'] . '/v4route?route=' . $request['argument'];
                    $data = json_decode(fetchdata($url), true);
                    break;
                case 'shbgpipv6':
                    $url = 'http://' . $_SESSION['router']['host'] . ':' . $_SESSION['router']['port'] . '/v6route?route=' . $request['argument'];
                    $data = json_decode(fetchdata($url), true);
                    break;
            }
        }
    }
    echo $twig->render('lg.twig', ['config' => $config, 'errors' => $errors, 'data' => $data, 'request' => $request, 'routers' => $routers, 'active' => $_SESSION['router']['name'], 'communitylookup' => $communitylookup]);
}
