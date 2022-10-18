<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
class mfms
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

    public function getOptions($mfClass){
        $fields = $this->modx->getFields($mfClass['class']);
        foreach($fields as $field=>$v){
            if($field == "id") continue;
            if(!$opt = $this->modx->getObject("mfOption",['class_id'=>$mfClass['id'],'key'=>$field])){
                if($opt = $this->modx->newObject("mfOption",[
                    'class_id'=>$mfClass['id'],
                    'key'=>$field,
                    'alias'=>$field,
                    'filter_id'=>1,
                    'active'=>1,
                    ])){
                        $opt->save();
                    }
            }
        }
        return $this->mf->success("field",['fields'=>$fields]);
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
        }
    }
}