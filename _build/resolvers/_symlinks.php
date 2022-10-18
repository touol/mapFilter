<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/mapFilter/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/mapfilter')) {
            $cache->deleteTree(
                $dev . 'assets/components/mapfilter/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/mapfilter/', $dev . 'assets/components/mapfilter');
        }
        if (!is_link($dev . 'core/components/mapfilter')) {
            $cache->deleteTree(
                $dev . 'core/components/mapfilter/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/mapfilter/', $dev . 'core/components/mapfilter');
        }
    }
}

return true;