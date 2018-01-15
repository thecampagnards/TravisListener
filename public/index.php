<?php

header('Content-Type: application/json');

$json_data = file_get_contents('../tokens.json');
$tokens=json_decode($json_data);

if($tokens === FALSE){
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

// Check in the tokens
foreach ($tokens as &$token){
    if($token->token === $_GET['token']){
        $token->valid = true;
        break;
    }
}

// Check the token
if(empty($token->valid)){
    http_response_code(304);
    print(json_encode('Bad token'));
    exit(1);
}

// Call script project
// Good
if(exec(escapeshellcmd(dirname(__FILE__) . '/../scripts/install.sh '.$token->project.' 2>&1; echo $?')) == 0){
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