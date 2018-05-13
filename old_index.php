<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BGP Sessions @ AS62078</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>

    <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
      <h5 class="my-0 mr-md-auto font-weight-normal">AS62078</h5>
      <nav class="my-2 my-md-0 mr-md-3">
        <a class="p-2 text-dark" href="#">Looking Glass</a>
        <a class="p-2 text-dark" href="https://www.as62078.net/">Peering Policy</a>
      </nav>
    </div>


    <div class="container">

<h1>BGP Sessions</h1>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">BGP</a></li>
    <li class="breadcrumb-item active" aria-current="page">IPv4</li>
  </ol>
</nav>

<?php

$url = "http://185.117.14.254:1232/v4";
$data = json_decode(file_get_contents($url),true);

$peersnew = array();

foreach($data["ipv4Unicast"]["peers"] as $peer => $parameter) {
  $count = NULL;
  foreach($parameter as $key => $value) {
    $peersnew[$parameter["remoteAs"]][$peer][$key] = $value;
  }
}


echo '
<table class="table table-striped">
  <thead class="thead-dark">
    <tr>
      <th scope="col">AS</th>
      <th scope="col">Neighbor IP</th>
      <th scope="col">State</th>
      <th scope="col">Uptime</th>
      <th scope="col">Prefix Count</th>
      <th scope="col">msgRcvd</th>
      <th scope="col">msgSent</th>
    </tr>
';

foreach($peersnew as $asn => $sessions) {
  foreach($sessions as $session => $parameter) {
    echo '<tr>';

    // ASN
    echo '<td>' . $asn . '</td>';

    // Peer IP
    echo '<td>' . $session . '</td>';

    // State
    if($parameter["state"] == "Established") {
      echo '<h1><td><span class="badge badge-success">Established</span></td></h1>';
    } else {
      echo '<td><span class="badge badge-warning">' . $parameter["state"] . '</span></td>';
    }

    // Uptime
    echo '<td>' . $parameter["peerUptime"] . '</td>';

    // Prefix Count
    echo '<td>' . $parameter["prefixReceivedCount"] . '</td>';

    // MSG rcvd / sent
    echo '<td>' . $parameter["msgRcvd"] . '</td>';
    echo '<td>' . $parameter["msgSent"] . '</td>';
    echo '</tr>';
  }
}


?>

  </tbody>
</table>

</div>
