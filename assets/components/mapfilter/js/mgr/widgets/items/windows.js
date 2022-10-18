mapFilter.window.CreateItem = function (config) {
    config = config || {}
    config.url = mapFilter.config.connector_url

    Ext.applyIf(config, {
        title: _('mapfilter_item_create'),
        width: 600,
        cls: 'mapfilter_windows',
        baseParams: {
            action: 'mgr/item/create',
            resource_id: config.resource_id
        }
    })
    mapFilter.window.CreateItem.superclass.constructor.call(this, config)

    this.on('success', function (data) {
        if (data.a.result.object) {
            // Авто запуск при создании новой подписик
            if (data.a.result.object.mode) {
                if (data.a.result.object.mode === 'new') {
                    var grid = Ext.getCmp('mapfilter-grid-items')
                    grid.updateItem(grid, '', {data: data.a.result.object})
                }
            }
        }
    }, this)
}
Ext.extend(mapFilter.window.CreateItem, mapFilter.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {
                xtype: 'textfield',
                fieldLabel: _('mapfilter_item_name'),
                name: 'name',
                id: config.id + '-name',
                anchor: '99%',
                allowBlank: false,
            }, {
                xtype: 'textarea',
                fieldLabel: _('mapfilter_item_description'),
                name: 'description',
                id: config.id + '-description',
                height: 150,
                anchor: '99%'
            },  {
                xtype: 'mapfilter-combo-filter-resource',
                fieldLabel: _('mapfilter_item_resource_id'),
                name: 'resource_id',
                id: config.id + '-resource_id',
                height: 150,
                anchor: '99%'
            }, {
                xtype: 'xcheckbox',
                boxLabel: _('mapfilter_item_active'),
                name: 'active',
                id: config.id + '-active',
                checked: true,
            }
        ]


    }
})
Ext.reg('mapfilter-item-window-create', mapFilter.window.CreateItem)

mapFilter.window.UpdateItem = function (config) {
    config = config || {}

    Ext.applyIf(config, {
        title: _('mapfilter_item_update'),
        baseParams: {
            action: 'mgr/item/update',
            resource_id: config.resource_id
        },
    })
    mapFilter.window.UpdateItem.superclass.constructor.call(this, config)
}
Ext.extend(mapFilter.window.UpdateItem, mapFilter.window.CreateItem)
Ext.reg('mapfilter-item-window-update', mapFilter.window.UpdateItem)