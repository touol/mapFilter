<?php

/**
 * The home manager controller for mapFilter.
 *
 */
class mapFilterHomeManagerController extends modExtraManagerController
{
    /** @var mapFilter $mapFilter */
    public $mapFilter;


    /**
     *
     */
    public function initialize()
    {
        $this->mapFilter = $this->modx->getService('mapFilter', 'mapFilter', MODX_CORE_PATH . 'components/mapfilter/model/');
        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['mapfilter:manager', 'mapfilter:default'];
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('mapfilter');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->mapFilter->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->mapFilter->config['jsUrl'] . 'mgr/mapfilter.js');
        $this->addJavascript($this->mapFilter->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->mapFilter->config['jsUrl'] . 'mgr/misc/combo.js');
        $this->addJavascript($this->mapFilter->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $this->addJavascript($this->mapFilter->config['jsUrl'] . 'mgr/misc/default.window.js');
        $this->addJavascript($this->mapFilter->config['jsUrl'] . 'mgr/widgets/items/grid.js');
        $this->addJavascript($this->mapFilter->config['jsUrl'] . 'mgr/widgets/items/windows.js');
        $this->addJavascript($this->mapFilter->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->mapFilter->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addJavascript(MODX_MANAGER_URL . 'assets/modext/util/datetime.js');

        $this->mapFilter->config['date_format'] = $this->modx->getOption('mapfilter_date_format', null, '%d.%m.%y <span class="gray">%H:%M</span>');
        $this->mapFilter->config['help_buttons'] = ($buttons = $this->getButtons()) ? $buttons : '';

        $this->addHtml('<script type="text/javascript">
        mapFilter.config = ' . json_encode($this->mapFilter->config) . ';
        mapFilter.config.connector_url = "' . $this->mapFilter->config['connectorUrl'] . '";
        Ext.onReady(function() {MODx.load({ xtype: "mapfilter-page-home"});});
        </script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        $this->content .=  '<div id="mapfilter-panel-home-div"></div>';
        return '';
    }

    /**
     * @return string
     */
    public function getButtons()
    {
        $buttons = null;
        $name = 'mapFilter';
        $path = "Extras/{$name}/_build/build.php";
        if (file_exists(MODX_BASE_PATH . $path)) {
            $site_url = $this->modx->getOption('site_url').$path;
            $buttons[] = [
                'url' => $site_url,
                'text' => $this->modx->lexicon('mapfilter_button_install'),
            ];
            $buttons[] = [
                'url' => $site_url.'?download=1&encryption_disabled=1',
                'text' => $this->modx->lexicon('mapfilter_button_download'),
            ];
            $buttons[] = [
                'url' => $site_url.'?download=1',
                'text' => $this->modx->lexicon('mapfilter_button_download_encryption'),
            ];
        }
        return $buttons;
    }
}