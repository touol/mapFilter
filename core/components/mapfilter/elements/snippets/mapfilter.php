<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var mapFilter $mapFilter */
$mapFilter = $modx->getService('mapFilter', 'mapFilter', MODX_CORE_PATH . 'components/mapfilter/model/', $scriptProperties);
if (!$mapFilter) {
    return 'Could not load mapFilter class!';
}

// Do your snippet code here. This demo grabs 5 items from our custom table.
$tpl = $modx->getOption('tpl', $scriptProperties, 'Item');
$sortby = $modx->getOption('sortby', $scriptProperties, 'name');
$sortdir = $modx->getOption('sortbir', $scriptProperties, 'ASC');
$limit = $modx->getOption('limit', $scriptProperties, 5);
$outputSeparator = $modx->getOption('outputSeparator', $scriptProperties, "\n");
$toPlaceholder = $modx->getOption('toPlaceholder', $scriptProperties, false);
$category_id = $modx->resource->get("id");
$hash = sha1(serialize($scriptProperties));
$_SESSION['mapFilter'][$hash] = $scriptProperties;

$config = [
    'actionUrl' => $modx->getOption('assets_url'). 'components/mapfilter/action.php',
];
$config_js = preg_replace(array('/^\n/', '/\t{5}/'), '', '
                            mapFilter = {};
                            mapFilterConfig = ' . $modx->toJSON($config) . ';
                    ');
//$modx->regClientCSS('/assets/components/barcode/css/web/default.css');
$path = $modx->getOption('assets_url').'components/mapfilter/';
if($js) $modx->regClientStartupScript("<script type=\"text/javascript\">\n" . $config_js . "\n</script>", true);    
if($js) $modx->regClientScript($path.$js);
if($css) $modx->regClientCSS($path.$css);
$mapFilter->addTime('mapFilter start');
$mapFilter->setCategoryChilds($category_id);
$_REQUEST['category_id'] = $category_id;
$filters = $mapFilter->getFilter($category_id);
$_REQUEST['suggestions'] = false;
$response = $mapFilter->filter($_REQUEST);
$results = $response['data']['results'];
$output = [
    'hash'=>$hash,
    'category_id'=>$category_id,
    'filters'=>$filters,
    'results'=>$results,
    'log'=>$response['data']['log'],
];
// Output
$output = $mapFilter->pdo->getChunk($scriptProperties['tplOuter'], $output);
// if (!empty($toPlaceholder)) {
//     // If using a placeholder, output nothing and set output to specified placeholder
//     $modx->setPlaceholder($toPlaceholder, $output);

//     return '';
// }
// By default just return output
return $output;
