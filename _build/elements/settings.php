<?php

return [
    /*'combo_boolean' => [
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'mapfilter_main',
    ],*/
    
    'handler_class' => [
      'xtype' => 'textfield',
      'value' => 'mapFilterHandler',
      'area' => 'mapfilter_main',
    ],
    'admin' => [
        'xtype' => 'textfield',
        'value' => '{
          "loadModels": "mapfilter",
          "selects": {
            "class": {
              "type": "select",
              "class": "mfClass",
              "pdoTools": {
                "class": "mfClass",
                "select": "mfClass.id,mfClass.alias"
              },
              "content": "{$alias}"
            },
            "field_type": {
              "type": "select",
              "class": "mfFieldType",
              "pdoTools": {
                "class": "mfFieldType",
                "select": "mfFieldType.id,mfFieldType.name"
              },
              "content": "{$name}"
            },
            "filter": {
              "type": "select",
              "class": "mfFilter",
              "pdoTools": {
                "class": "mfFilter",
                "select": "mfFilter.id,mfFilter.name"
              },
              "content": "{$name}"
            }
          },
          "tabs": {
            "mfOption": {
              "label": "Опции",
              "table": {
                "class": "mfOption",
                "actions": {
                  "create": [],
                  "update": [],
                  "update_options": {
                    "cls": "btn blue",
                    "text": "get_ms_val_and_resource_options",
                    "long_process": 1,
                    "action": "mapfilter/get_ms_val_and_resource_options",
                    "multiple": {
                      "title": "get_ms_val_and_resource_options"
                    }
                  },
                  "remove": []
                },
                "pdoTools": {
                  "class": "mfOption"
                },
                "checkbox": 1,
                "autosave": 1,
                "row": {
                  "id": [],
                  "class_id": {
                    "label": "Класс таблицы",
                    "edit": {
                      "type": "select",
                      "select": "class"
                    },
                    "filter": 1
                  },
                  "key": {
                    "label": "Ключ",
                    "filter": 1
                  },
                  "alias": {
                    "label": "Алиас"
                  },
                  "field_type_id": {
                    "label": "Тип поля",
                    "edit": {
                      "type": "select",
                      "select": "field_type"
                    },
                    "filter": 1
                  },
                  "filter_id": {
                    "label": "Тип фильтра",
                    "edit": {
                      "type": "select",
                      "select": "filter"
                    },
                    "filter": 1
                  },
                  "label": {
                    "label": "Заголовок фильтра",
                    "filter": 1
                  },
                  "outer_chunk": {
                    "label": "Чанк фильтра"
                  },
                  "row_chunk": {
                    "label": "Чанк строки фильтра"
                  },
                  "show_colpased": {
                    "label": "Фильтер свернут",
                    "filter": 1,
                    "edit": {
                      "type": "checkbox"
                    },
                    "default": 1
                  },
                  "active": {
                    "label": "Активно",
                    "filter": 1,
                    "edit": {
                      "type": "checkbox"
                    },
                    "default": 1
                  }
                }
              }
            },
            "mfClass": {
              "label": "Классы опций",
              "table": {
                "class": "mfClass",
                "actions": {
                  "create": [],
                  "update": [],
                  "update_options": {
                    "cls": "btn blue",
                    "text": "Обновить таблицу опций",
                    "action": "mapfilter/get_options",
                    "multiple": {
                      "title": "Обновить таблицу опций"
                    }
                  },
                  "remove": []
                },
                "pdoTools": {
                  "class": "mfClass"
                },
                "checkbox": 1,
                "autosave": 1,
                "row": {
                  "id": [],
                  "alias": {
                    "label": "Алиас таблицы"
                  },
                  "class": {
                    "label": "Класс таблицы"
                  },
                  "class_php": {
                    "label": "Класс обработчика"
                  },
                  "class_php_path": {
                    "label": "Путь к классу обработчика"
                  },
                  "active": {
                    "label": "Активно",
                    "filter": 1,
                    "edit": {
                      "type": "checkbox"
                    },
                    "default": 1
                  }
                }
              }
            },
            "mfFieldType": {
              "label": "Типы полей",
              "table": {
                "class": "mfFieldType",
                "actions": {
                  "create": [],
                  "update": [],
                  "remove": []
                },
                "pdoTools": {
                  "class": "mfFieldType"
                },
                "checkbox": 1,
                "autosave": 1,
                "row": {
                  "id": [],
                  "name": {
                    "label": "Тип поля таблицы"
                  }
                }
              }
            },
            "mfFilter": {
              "label": "Типы фильтров",
              "table": {
                "class": "mfFilter",
                "actions": {
                  "create": [],
                  "update": [],
                  "remove": []
                },
                "pdoTools": {
                  "class": "mfFilter"
                },
                "checkbox": 1,
                "autosave": 1,
                "row": {
                  "id": [],
                  "name": {
                    "label": "Тип"
                  }
                }
              }
            }
          }
        }',
        'area' => 'mapfilter_main',
    ],
];