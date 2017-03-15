<?php
include(dirname(__FILE__) . '/../../resources/prepend.php');

$log = new Log();

// log a failure and quit
if (isset($_GET['fail'])) {
    $log->logEvent('SMS_FAIL', json_encode($_POST));
    exit();
}

// log a success
$log->logEvent('SMS_SUCCESS', json_encode($_POST));

// save the image
if (isset($_POST['NumMedia']) && $_POST['NumMedia'] > 0) {
    $media = new Media();

    // add each image to database
    for ($i = 0; $i < intval($_POST['NumMedia']); $i++) {
        $media->addMedia($_POST['From'], $_POST["MediaUrl$i"]);
    }
}

$body = strtolower($_POST['Body']);
$regex = '/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/';

// check if body contains email
if (preg_match($regex, $body, $email)) {
    $account = new Account();
    $account->updateEmail($_POST['From'], $email[0]);
} else {
    switch(strtolower($body)) {
        case 'profile':
            $account->updateProfile($_POST['From'], $media_sid);
            break;
        default:
            // unknown command
    }
}
