<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
class mapFilterHandler
{
    /** @var modX $modx */
    public $modx;

    /** @var array() $config */
    public $config = array();
    
    public function __construct(mapFilter &$mf, array $config = array()) {
        $this->modx =& $mf->modx;
        $this->pdo =& $mf->pdo;
        $this->mf =& $mf;

        $this->config = array_merge(array(
            
        ), $config);
    }

    public function getTvPdoOptions($mfClass){
        $this->pdo->setConfig([
            'class'=>'mfFieldType',
            'where'=>[
                'alias'=>'tv'
            ],
            'return'=>'data',
            'limit'=>0,
            ]);
        $mfFieldType0 = $this->pdo->run();
        $mfFieldType = [];
        foreach($mfFieldType0 as $v){
            $mfFieldType[] = $v['name'];
        }
        return [
            'class'=>'modTemplateVar',
            'where'=>[
                'type:IN'=>$mfFieldType
            ],
            'return'=>'data',
            'setTotal'=>1,
        ];
    }
    public function setTvOptions($options, $mfClass){
        $this->pdo->setConfig([
            'class'=>'mfFieldType',
            'where'=>[
                'alias'=>'tv'
            ],
            'return'=>'data',
            'limit'=>0,
            ]);
        $mfFieldType0 = $this->pdo->run();
        $mfFieldType = [];
        foreach($mfFieldType0 as $v){
            $mfFieldType[$v['name']] = $v['id'];
        }
        foreach($options as $option){
            if(!$opt = $this->modx->getObject("mfOption",['class_id'=>$mfClass['id'],'option_native_id'=>$option['id']])){
                $c = $this->modx->newQuery("mfOption");
                $c->select("MAX(sort) as max");
                $c->where($where);
                $max = 0;
                if ($c->prepare() && $c->stmt->execute()) {
                    $max = $c->stmt->fetchColumn();
                }

                if($opt = $this->modx->newObject("mfOption",[
                    'sort'=>$max + 1,
                    'class_id'=>$mfClass['id'],
                    'option_native_id'=>$option['id'],
                    'key'=>$option['name'],
                    'alias'=>$option['name'],
                    'field_type_id'=>$mfFieldType[$option['type']]['id'],
                    'filter_id'=>1,
                    'label'=>$option['caption'],
                    //'outer_chunk'=>,
                    //'row_chunk'=>,
                    //'show_colpased'=>,
                    //'include_results_query'=>,
                    'active'=>1,
                    ])){
                        $opt->save();
                    }
            }
        }
        return true;
    }
    public function getTvPdoMapOptions($mfClass){
        return [
            'class'=>'mfOption',
            'leftJoin'=>[
                'mfFieldType'=>[
                    'class'=>'mfFieldType',
                    'on'=>'mfFieldType.id = mfOption.field_type_id'
                ],
                'modTemplateVar'=>[
                    'class'=>'modTemplateVar',
                    'on'=>'modTemplateVar.id = mfOption.option_native_id'
                ],
                'modTemplateVarResource'=>[
                    'class'=>'modTemplateVarResource',
                    'on'=>'modTemplateVarResource.tmplvarid = mfOption.option_native_id'
                ],
            ],
            'groupby'=>'mfOption.id,modTemplateVarResource.value',
            'where'=>[
                'mfOption.active'=>1,
                'mfOption.class_id'=>$mfClass['id'],
            ],
            'select'=>[
                'mfOption'=>'*',
                'mfFieldType'=>'mfFieldType.name as field_type_name',
                'modTemplateVar'=>'modTemplateVar.default_text',
                'modTemplateVarResource'=>'modTemplateVarResource.value,CONCAT_WS(",",modTemplateVarResource.contentid) as resources',
            ],
            'return'=>'data',
            'setTotal'=>1,
        ];
    }
    public function setTvMapOptions($options, $mfClass){
        
        foreach($options as $option){
            switch($option['field_type_name']){
                case 'text':
                    $values = [$option['value']];
                    break;
                case 'checkbox':
                    $values = [$option['value']];
                    break;
                case 'autotag':
                    $values = array_map('trim',explode(",",$option['value']));
                    break;
            }

            foreach($values as $value){
                if(!$mfOptVal = $this->modx->getObject("mfOptVal",['option_id'=>$option['id'],'value'=>$value])){
                    if($mfOptVal = $this->modx->newObject("mfOptVal",['option_id'=>$option['id'],'value'=>$value])){
                        if($mfOptVal->save()){
                            $ids =  explode(",",$option['resources']);
                            foreach($ids as $id){
                                if($mfResVal = $this->modx->newObject("mfResVal",['value_id'=>$mfOptVal->id,'resource_id'=>$id])){
                                    $mfResVal->save();
                                }
                            }
                        }
                    }
                }
            }
            
            if(!empty($option['default_text'])){
                if(strpos($option['default_text'], '==') !== false){
                    $tmp = explode('==', $option['default_text']);
                    $value = $tmp[1];
                }else{
                    $value = $option['default_text'];
                }
                if(!$mfOptVal = $this->modx->getObject("mfOptVal",['option_id'=>$option['id'],'value'=>$value])){
                    $this->pdo->setConfig([
                        'class'=>'modResource',
                        'leftJoin'=>[
                            'modTemplateVarTemplate'=>[
                                'class'=>'modTemplateVarTemplate',
                                'on'=>'modResource.template = modTemplateVarTemplate.templateid'
                            ],
                            'modTemplateVarResource'=>[
                                'class'=>'modTemplateVarResource',
                                'on'=>'modTemplateVarResource.contentid = modResource.id'
                            ],
                        ],
                        'where'=>[
                            'modTemplateVarTemplate.tmplvarid'=>$option['option_native_id'],
                            'modTemplateVarResource.value IS NULL',
                        ],
                        'return'=>'ids',
                        'limit'=>0,
                        ]);
                    $ids = $this->pdo->run();
                    if($mfOptVal = $this->modx->newObject("mfOptVal",['option_id'=>$option['id'],'value'=>$value])){
                        if($mfOptVal->save()){
                            $ids = explode(",",$ids);
                            foreach($ids as $id){
                                if($mfResVal = $this->modx->newObject("mfResVal",['value_id'=>$mfOptVal->id,'resource_id'=>$id])){
                                    $mfResVal->save();
                                }
                            }
                        }
                    }
                }
            }
            
        }
        return true;
    }
}