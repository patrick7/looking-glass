<?php
session_start();
include('config.php');
if($config['debug']) {
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  var_dump($_SESSION);
}

require_once 'vendor/autoload.php';


if(!file_exists('config.php')) {
  die('Configuration not existing');
}
if(!isset($routers) || empty($routers)) {
  die('Routers not set');
}

if(!isset($_SESSION['router'])) {
  $_SESSION['router'] = $routers[0];
}
if(isset($_GET['router'])) {
  if(!empty($routers[$_GET['router']])) {
    $_SESSION['router'] = $routers[$_GET['router']];
  } else {
    die('invalid router provided');
  }
}

function validateIP($ip){
  if(strpos($ip, '/') !== false) {
    $ip = explode('/', $ip);
    $address = $ip[0];
    $mask = $ip[1];
  } else {
    $address = $ip;
  }
  if(isset($mask) && !is_numeric($mask)) {
    return false;
  } elseif(@inet_pton($address) !== false) {
    return true;
  } else {
    return false;
  }
}
function ipVersion($txt) {
     return strpos($txt, ":") === false ? 4 : 6;
}

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);


/*
 * Base
*/
$request = array();
if(isset($_GET['site'])) {
  $request['site'] = $_GET['site'];
}


/*
 * Peering Summary
*/

if(!isset($_GET['site'])) {
  $data = json_decode(file_get_contents('http://' . $_SESSION['router']['host'] . ':' . $_SESSION['router']['port'] . '/neighbors'),true);
  $peersnew = array();
  $sessionid = 0;
  foreach($data as $peer => $parameter) {
    foreach($parameter as $key => $value) {
      $peersnew[$parameter['remoteAs']][$peer]['sessionid'] = $sessionid;
      $peersnew[$parameter['remoteAs']][$peer][$key] = $value;
    }
    $sessionid++;
  }

  echo $twig->render('peers.tpl', [ 'data' => $peersnew, 'config' => $config, 'routers' => $routers, 'active' => $_SESSION['router']['name'] ]);
}


/*
 * Looking Glass
*/
if(isset($_GET['site']) && $_GET['site'] == 'lg') {
  $data = array();

  // Errors
  $errors = array();

  if(isset($_POST) && !empty($_POST)) {

    // -> Command
    if(isset($_POST['command'])) {
      $request['command'] = trim($_POST['command']);

      if(empty($request['command'])) {
        $errors[] = 'command-empty';
      }
    } else {
      $errors[] = 'command-notset';
    }

    // -> Argument
    if(isset($_POST['argument'])) {
      $request['argument'] = trim($_POST['argument']);

      if(empty($request['argument'])) {
        $errors[] = 'argument-empty';
      } elseif(!validateIP($request['argument'])) {
        $errors[] = 'argument-invalid';
      }
    } else {
      $erorrs[] = 'argument-notset';
    }

    // -> Address family
    if(isset($request['command']) && isset($request['argument']) && !in_array('argument-invalid', $errors)) {
      if(ipVersion($request['argument']) == '4' && $request['command'] == 'shbgpipv6') {
        $errors[] = 'afi-ipv6-prefix-ipv4';
      } elseif(ipVersion($request['argument']) == '6' && $request['command'] == 'shbgpipv4') {
        $errors[] = 'afi-ipv4-prefix-ipv6';
      }
    }

    // Load LG
    if(empty($errors)) {
      switch($request['command']) {
        case 'shbgpipv4':
          $url = 'http://' . $_SESSION['router']['host'] . ':' . $_SESSION['router']['port'] . '/v4route?route=' . $request['argument'];
          $data = json_decode(file_get_contents($url),true);
          break;
        case 'shbgpipv6':
          $url = 'http://' . $_SESSION['router']['host'] . ':' . $_SESSION['router']['port'] . '/v6route?route=' . $request['argument'];
          $data = json_decode(file_get_contents($url),true);
          break;
      }
    }
  }
  echo $twig->render('lg.tpl', [ 'config' => $config, 'errors' => $errors, 'data' => $data, 'request' => $request, 'routers' => $routers, 'active' => $_SESSION['router']['name'] ]);
}
?>
