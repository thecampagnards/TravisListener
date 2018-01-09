<?php

header('Content-Type: application/json');

$token=file_get_contents('../token');

if($token === FALSE){
    http_response_code(500);
    print(json_encode('Please generate a token'));
    exit(1); 
}

// Check get params not empty
if(empty($_GET['project']) && empty($_GET['token'])){
    http_response_code(404);
    print(json_encode('Bad params'));
    exit(1);
}

// Check the token
if($_GET['token'] !== $token){
    http_response_code(304);
    print(json_encode('Bad token'));
    exit(1);
}

// Call script project
// Good
if(exec(escapeshellcmd(dirname(__FILE__) . '/../scripts/listener.sh '.$_GET['project'].' 2>&1; echo $?')) === 0){
    http_response_code(200);
    print(json_encode(['update' => 'good']));
    exit(0);
}
// Bad
else{
    http_response_code(500);
    print(json_encode(['update' => 'bad']));
    exit(1);
}