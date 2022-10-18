<?php
if (empty($_REQUEST['action']) and empty($_REQUEST['mapfilter_action'])) {
    $message = 'Access denied action.php';
    echo json_encode(
            ['success' => false,
            'message' => $message,]
            );
    return;
}

define('MODX_API_MODE', true);
require dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

$_REQUEST['action'] = $_REQUEST['action'] ? $_REQUEST['action'] : $_REQUEST['mapfilter_action'];

$sp = [];
if($_REQUEST['hash']){
    $sp = $_SESSION['mapFilter'][$_REQUEST['hash']];
}
if(!$mapfilter = $modx->getService("mapfilter","mapfilter",
    MODX_CORE_PATH."components/mapfilter/model/",$sp)){
    $message =  'Could not create mapfilter!';
    echo json_encode(
        ['success' => false,
        'message' => $message,]
        );
    return;
}

$modx->lexicon->load('mapfilter:default');

$response = $mapfilter->handleRequest($_REQUEST['action'],$_REQUEST);

echo json_encode($response);
exit;