var mapFilter = function (config) {
    config = config || {};
    mapFilter.superclass.constructor.call(this, config);
};
Ext.extend(mapFilter, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}, buttons: {}
});
Ext.reg('mapfilter', mapFilter);

mapFilter = new mapFilter();