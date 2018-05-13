<?php

$ip = "2600::v";

function validateIP($ip){
    return inet_pton($ip) !== false;
}



var_dump(validateIP($ip));
