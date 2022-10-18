<?php
/** @var modX $modx */
/* @var array $scriptProperties */
switch ($modx->event->name) {
    case 'OnHandleRequest':
        /* @var mapFilter $mapFilter*/
        $mapFilter = $modx->getService('mapfilter', 'mapFilter', $modx->getOption('mapfilter_core_path', $scriptProperties, $modx->getOption('core_path') . 'components/mapfilter/') . 'model/');
        if ($mapFilter instanceof mapFilter) {
            $mapFilter->loadHandlerEvent($modx->event, $scriptProperties);
        }
        break;
}
return '';