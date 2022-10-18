mapFilter.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'mapfilter-panel-home',
            renderTo: 'mapfilter-panel-home-div'
        }]
    });
    mapFilter.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(mapFilter.page.Home, MODx.Component);
Ext.reg('mapfilter-page-home', mapFilter.page.Home);