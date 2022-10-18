(function (window, document, $, mapFilterConfig) {
    var mapFilter = mapFilter || {};
    mapFilterConfig.callbacksObjectTemplate = function () {
        return {
            // return false to prevent send data
            before: [],
            response: {
                success: [],
                error: []
            },
            ajax: {
                done: [],
                fail: [],
                always: []
            }
        }
    };
    mapFilter.setup = function () {
        // selectors & $objects
        this.actionName = 'mapfilter_action';
        this.action = ':submit[name=' + this.actionName + ']';
        this.form = '.mapFilter_form';
        this.$doc = $(document);
        
        this.sendData = {
            $form: null,
            action: null,
            formData: null
        };
        
        this.timeout = 300;
    };
    mapFilter.initialize = function () {
        mapFilter.setup();
                        
        mapFilter.Filter.initialize();
    };
    mapFilter.controller = function () {
        var self = this;
        //console.info(self.sendData.action);
        switch (self.sendData.action) {
            // case 'report/update':
            //     mapFilter.Report.update();
            //     break;
            // case 'report/update_file':
            //     mapFilter.Report.update_file();
            //     break;
            default:
                return;
        }
    };
    mapFilter.send = function (data, callbacks) {
        var runCallback = function (callback, bind) {
            if (typeof callback == 'function') {
                return callback.apply(bind, Array.prototype.slice.call(arguments, 2));
            }
            else if (typeof callback == 'object') {
                for (var i in callback) {
                    if (callback.hasOwnProperty(i)) {
                        var response = callback[i].apply(bind, Array.prototype.slice.call(arguments, 2));
                        if (response === false) {
                            return false;
                        }
                    }
                }
            }
            return true;
        };

        var url = (mapFilterConfig.actionUrl)
                      ? mapFilterConfig.actionUrl
                      : document.location.href;
        // send
        var xhr = function (callbacks) {
            return $.post(url, data, function (response) {
                if (response.success) {
                    // if (response.message) {
                    //     getTables.Message.success(response.message);
                    // }
                    //console.info(callbacks.response.success);
                    runCallback(callbacks.response.success, mapFilter, response);
                }
                else {
                    //getTables.Message.error(response.message);
                    runCallback(callbacks.response.error, mapFilter, response);
                }
            }, 'json')
        }(callbacks);
    };
    

    mapFilter.Filter = {
        callbacks: {
            filter: mapFilterConfig.callbacksObjectTemplate(),
        },
        setup: function () {
        },
        initialize: function () {
            mapFilter.$doc
            .on('change', '.mapfilter-value', function (e) {
                e.preventDefault();
                mapFilter.Filter.filter(this);
            });
            
        },
        filter: function (el) {
            $mapFilterOuter = $(el).closest('.mapfilter-outer');
            let action = 'filter';
            formData ={
                action:action,
                hash:$mapFilterOuter.data('hash'),
                category_id:$mapFilterOuter.data('category_id')
            };
            filters = mapFilter.Filter.getFilter($mapFilterOuter);
            
            $.each(filters, function( key, value ) {
                formData[key] = value;
            });

            mapFilter.sendData = {
                $mapFilterOuter: $mapFilterOuter,
                action: action,
                formData: formData
            };
            
            var callbacks = mapFilter.Filter.callbacks;
            callbacks.filter.response.success = function (response) {
                if(response.data.results){
                    mapFilter.sendData.$mapFilterOuter.find('.mapfilter-results').html(response.data.results);
                }
                if(response.data.suggestions){
                    mapFilter.Filter.setSuggestions(mapFilter.sendData.$mapFilterOuter,response.data.suggestions);
                }
                if(response.data.log){
                    mapFilter.sendData.$mapFilterOuter.find('.mapFilterLog').html(response.data.log);
                }
            };
            
            mapFilter.send(mapFilter.sendData.formData, mapFilter.Filter.callbacks.filter);
        },
        setSuggestions: function ($mapFilterOuter,suggestions) {
            var count;
            for (var alias in suggestions){
                $filter = $mapFilterOuter.find('.mapfilter-'+alias);
                if($filter.length != 1) continue;
                var values = suggestions[alias];
                for(var value in values){
                    count = values[value];
                    var $input = $filter.find('.mapfilter-value[value="'+value+'"]');
                    var elem = $input.parent().find('sup');
                    elem.text(count);
                }
                
            }
        },
        getFilter: function ($mapFilterOuter) {
            var params = {};
            $mapFilterOuter.find('input.mapfilter-value:checked').each(function(){
                if(params[$(this).attr('name')]){
                    params[$(this).attr('name')] += ";"+$(this).val();
                }else{
                    params[$(this).attr('name')] = $(this).val();
                }
            });
            return params;
        },
        
    };
    
    $(document).ready(function ($) {
        mapFilter.initialize();
        var html = $('html');
        html.removeClass('no-js');
        if (!html.hasClass('js')) {
            html.addClass('js');
        }
    });

    window.mapFilter = mapFilter;
})(window, document, jQuery, mapFilterConfig);