<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
class mapFilter
{
    /** @var modX $modx */
    public $modx;

    /** @var array() $config */
    public $config = [];

    /** @var array $initialized */
    public $initialized = [];

    public $options = [];
    public $timings = [];
    protected $start = 0;
    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;
        $corePath = MODX_CORE_PATH . 'components/mapfilter/';
        $assetsUrl = MODX_ASSETS_URL . 'components/mapfilter/';

        $this->config = array_merge([
            'corePath' => $corePath,
            'customPath' => $corePath . 'custom/',
            'modelPath' => $corePath . 'model/',
        ], $config);

        $this->modx->addPackage('mapfilter', $this->config['modelPath']);
        $this->modx->lexicon->load('mapfilter:default');

        $this->miniShop2 = $modx->getService('miniShop2');

        if ($this->pdo = $this->modx->getService('pdoFetch')) {
            $this->pdo->setConfig($this->config);
        }
        $this->timings = [];
        $this->time = $this->start = microtime(true);
    }
    /**
     * Add new record to time log
     *
     * @param $message
     * @param null $delta
     */
    public function addTime($message, $delta = null)
    {
        $time = microtime(true);
        if (!$delta) {
            $delta = $time - $this->time;
        }

        $this->timings[] = array(
            'time' => number_format(round(($delta), 7), 7),
            'message' => $message,
        );
        $this->time = $time;
    }
    /**
     * Return timings log
     *
     * @param bool $string Return array or formatted string
     *
     * @return array|string
     */
    public function getTime($string = true)
    {
        $this->timings[] = array(
            'time' => number_format(round(microtime(true) - $this->start, 7), 7),
            'message' => '<b>Total time</b>',
        );
        $this->timings[] = array(
            'time' => number_format(round((memory_get_usage(true)), 2), 0, ',', ' '),
            'message' => '<b>Memory usage</b>',
        );

        if (!$string) {
            return $this->timings;
        } else {
            $res = '';
            foreach ($this->timings as $v) {
                $res .= $v['time'] . ': ' . $v['message'] . "\n";
            }

            return $res;
        }
    }
    public function get_options($data)
    {
        $this->pdo->setConfig([
            'class'=>'mfClass',
            'where'=>[
                'active'=>1
            ],
            'return'=>'data',
            'limit'=>0,
            ]);
        $mfClasses = $this->pdo->run();
        if(count($mfClasses) == 0 or !is_array($mfClasses)) return $this->error("Not active class");
        foreach($mfClasses as $mfClass){
            $method = 'getOptions';
            if(empty($mfClass['class_php']) or empty($mfClass['class_php_path'])){
                return $this->error("No setting class {$mfClass['class']} {$mfClass['class_php']} {$mfClass['class_php_path']}");
            }
            $class_php = $mfClass['class_php']; 
            require_once MODX_CORE_PATH.$mfClass['class_php_path'];
            $class_object = new $class_php($this, $this->config);
            if (!method_exists($class_object, $method)) {
                return $this->success("Method $method not exists in class $class_php");
            }
            $resp = call_user_func_array([$class_object, $method],[$mfClass]);
            return $resp;
            if(!resp['success']) return $resp;
        }
        return $this->success("updated");
    }
    public function get_ms_val_and_resource_options($data)
    {
        
        
        $this->pdo->setConfig([
            'class'=>'mfOption',
            'leftJoin'=>[
                'mfFieldType'=>[
                    'class'=>'mfFieldType',
                    'on'=>'mfFieldType.id = mfOption.field_type_id',
                ]
            ],
            'where'=>[
                'mfOption.class_id'=>2,
                'mfOption.active'=>1,
            ],
            'select'=>[
                'mfOption'=>'*',
                'mfFieldType'=>'mfFieldType.name as ftname',
            ],
            'return'=>'data',
            'limit'=>0,
            ]);
        $mfOptions = $this->pdo->run();
        if(count($mfOptions) == 0 or !is_array($mfOptions)) return $this->success([
            'completed'=>true,
            'procent'=>0,
            'offset'=>0,
            'message'=>"Not active mfOptions"]);
        
        $step = (int)$data['limit'] ? (int)$data['limit'] : 50;
        $offset = (int)$data['offset'] ? (int)$data['offset'] : 0;
        $q = $this->modx->newQuery('msProductData');
        $total = $this->modx->getCount('msProductData', $q);
        $q->limit($step,$offset);
        $resources = $this->modx->getCollection('msProductData', $q);
        
        foreach ($resources as $resource) {
            foreach($mfOptions as $mfOption){
                $values = [];
                $sql = "INSERT INTO `modx_mapfilter_options_resource_values` (`resource_id`, `value_id`) VALUES ";//"DELETE FROM modx_mapfilter_options_resource_values WHERE user = 'jcole'"
                $sql_inserts = [];
                $value = $resource->{$mfOption['key']};
                switch($mfOption['ftname']){
                    case 'text':
                        if($value){
                            $values[]=$value;
                        }
                    break;
                    case 'JSON':
                        if($value){
                            $values=json_decode($value,1);
                        }
                    break;
                }
                foreach($values as $value){
                    if(!$mfOptVal = $this->modx->getObject("mfOptVal",['option_id'=>$mfOption['id'],'value'=>$value])){
                        $mfOptVal = $this->modx->newObject("mfOptVal",['option_id'=>$mfOption['id'],'value'=>$value]);
                        $mfOptVal->save();
                    }
                    $sql_inserts[] = "({$resource->id},{$mfOptVal->id})";
                }
                $sql .= implode(",",$sql_inserts).";";
                $this->modx->exec($sql);
            }
        }
        
        if($step == 0 or ($offset+$step >$total)){
            $max = $total;
            $completed = true;
            $procent = 100;
        }else{
            $max = $offset+$step;
            $completed = false;
            $procent = round($max / $total, 2) * 100;
        }
        return $this->success([
            'completed'=>$completed,
            'procent'=>$procent,
            'offset'=>$max,
            'message'=>"Run get_ms_val_and_resource_options $sql"]);
    }
    
    public function setCategoryChilds($category_id){
        $this->pdo->setConfig([
            'class'=>'modResource',
            'parents'=>$category_id,
            'select'=>[
                'modResource'=>'modResource.id',
            ],
            'return'=>'data',
            'limit'=>0,
            ]);
        $Childs = $this->pdo->run();
        $sql = "DELETE FROM modx_mapfilter_category_childs WHERE category_id = '$category_id';";
        $sql .= "INSERT INTO `modx_mapfilter_category_childs` (`resource_id`, `category_id`) VALUES ";
        $sql_inserts = [];
        foreach($Childs as $resource){
            $sql_inserts[] = "({$resource['id']},{$category_id})";
        }
        $sql .= implode(",",$sql_inserts).";";
        $this->modx->exec($sql);
    }
    
    public function getFilter($category_id){
        $this->addTime('getFilter');
        $options = $this->getOptions($category_id);
        $this->addTime('getFilter getOptions');
        $filters = $this->prepareRequest($_REQUEST);
        $this->addTime('getFilter prepareRequest');
        if($this->config['suggestions']){
            $options = $this->getSuggestions($filters,$options,$category_id)['options'];
            $this->addTime('getFilter getSuggestions');
        }
        if($this->config['suggestions2']){
            $options = $this->getSuggestions2($filters,$options,$category_id)['options'];
            $this->addTime('getFilter getSuggestions2');
        }
        $filters = [];
        foreach($options['mfOptions'] as $mfOption){
            $rows = [];
            foreach($mfOption['mfOptVals'] as $mfOptVal){
                $rows[] = $this->pdo->getChunk($this->config['tplFilter.row.default'], [
                    'filter_key'=>$mfOption['alias'],
                    'value'=>$mfOptVal['value'],
                    'title'=>$mfOptVal['value'],
                    'num'=>$mfOptVal['num'],
                ]);
            }
            $mfOption['rows'] = implode("<br>",$rows);
            $filters[] = $this->pdo->getChunk($this->config['tplFilter.outer.default'], $mfOption);
        }
        return $options['count']."<br>".implode("\n",$filters);
    }
    public function getPDOFilter($filters,$category_id){
        $innerJoin = [];
        $where = [];
        foreach($filters as $alias=>$filter){
            $innerJoin[$alias] = [
                'class'=>'mfResVal',
                'on'=>"$alias.resource_id = mfCategoryChild.resource_id",
            ];
            $where["$alias.value_id:IN"] = array_values($filter);
             
        }
        $where['mfCategoryChild.category_id'] = $category_id;//$data['category_id'];
        return [
            'class'=>'mfCategoryChild',
            'innerJoin'=>$innerJoin,
            'where'=>$where,
            //'groupby'=>'mfCategoryChild.resource_id',
            'sortby'=>[
                'mfCategoryChild.resource_id'=>'ASC',
            ],
            'return'=>'data',
            'limit'=>0,
        ];
    }
    public function getSuggestions2($filters,$options,$category_id){
        $pdo = $this->getPDOFilter($filters,$category_id);
        //$pdo['select'] = "COUNT(*) as res_count";
        //$this->pdo->setConfig($pdo);
        //$res_count = $this->pdo->run()[0]['res_count'];
        //$this->addTime("getSuggestions res_count=$res_count \n".$this->pdo->getTime());
        $suggestions = [];
        foreach($options['mfOptions'] as &$mfOption){
            
            $alias = $mfOption['alias'];
            $pdoSug = $pdo;
            $pdoSug['select'] = "COUNT(*) as res_count,$alias.value_id";
            if(isset($filters[$alias])){
                unset($pdoSug['where']["$alias.value_id:IN"]);
                $pdoSug['where']["$alias.value_id:NOT IN"] = array_values($filters[$alias]);
            }else{
                $pdoSug['innerJoin'][$alias] = [
                    'class'=>'mfResVal',
                    'on'=>"$alias.resource_id = mfCategoryChild.resource_id",
                ];
            }
            $pdoSug['innerJoin']["mfOptVal"] = [
                'class'=>'mfOptVal',
                'on'=>"$alias.value_id = mfOptVal.id",
            ];
            $pdoSug['where']["mfOptVal.option_id"] = $mfOption['id'];
            $pdoSug['groupby'] = "$alias.value_id";
            $pdoSug['sortby'] = "$alias.value_id";
            $this->pdo->setConfig($pdoSug);
            $counts = $this->pdo->run();
            $this->addTime("getSuggestions2");
            //$this->addTime("getSuggestions2 \n".$this->pdo->getTime());
            foreach($mfOption['mfOptVals'] as &$mfOptVal){
                if(isset($filters[$alias])){
                    if(isset($filters[$alias][$mfOptVal['id']])){
                        $mfOptVal['num'] = '';
                        $suggestions[$alias][$mfOptVal['value']] = '';
                    }else{
                        foreach($counts as $v){
                            if($v['value_id'] == $mfOptVal['id']){
                                $num = '+'.$v['res_count'];
                            }
                        }
                        if(!isset($num)) $num = 0;
                        $mfOptVal['num'] = $num;
                        $suggestions[$alias][$mfOptVal['value']] = $num;
                    }
                }else{
                    foreach($counts as $v){
                        if($v['value_id'] == $mfOptVal['id']){
                            $num = $v['res_count'];
                        }
                    }
                    if(!isset($num)) $num = 0;
                    $mfOptVal['num'] = $num;
                    $suggestions[$alias][$mfOptVal['value']] = $num;
                }
            }
        }
        
        return [
            'suggestions'=>$suggestions,
            'options'=>$options,
        ];
    }
    public function getSuggestions($filters,$options,$category_id){
        $pdo = $this->getPDOFilter($filters,$category_id);
        $pdo['select'] = "COUNT(*) as res_count";
        $this->pdo->setConfig($pdo);
        $res_count = $this->pdo->run()[0]['res_count'];
        $this->addTime("getSuggestions res_count=$res_count \n".$this->pdo->getTime());
        $suggestions = [];
        foreach($options['mfOptions'] as &$mfOption){
            foreach($mfOption['mfOptVals'] as &$mfOptVal){
                if(isset($filters[$mfOption['alias']])){
                    if(isset($filters[$mfOption['alias']][$mfOptVal['id']])){
                        $mfOptVal['num'] = '';
                        $suggestions[$mfOption['alias']][$mfOptVal['value']] = '';
                    }else{
                        $f = $filters;
                        $f[$mfOption['alias']][$mfOptVal['id']] = $mfOptVal['id'];
                        $pdo = $this->getPDOFilter($f,$category_id);
                        $pdo['select'] = "COUNT(*) as res_count";
                        $this->pdo->setConfig($pdo);
                        $count = $this->pdo->run()[0]['res_count'];
                        $this->addTime("getSuggestions count=$count ");
                        $this->addTime("getSuggestions count=$count \n ".$this->pdo->getTime());
                        $mfOptVal['num'] = '+'.($count-$res_count);
                        $suggestions[$mfOption['alias']][$mfOptVal['value']] = '+'.($count-$res_count);
                    }
                }else{
                    $f = $filters;
                    $f[$mfOption['alias']][$mfOptVal['id']] = $mfOptVal['id'];
                    $pdo = $this->getPDOFilter($f,$category_id);
                    $pdo['select'] = "COUNT(*) as res_count";
                    $this->pdo->setConfig($pdo);
                    $count = $this->pdo->run()[0]['res_count'];
                    $this->addTime("getSuggestions count=$count ");
                    $mfOptVal['num'] = $count;
                    $suggestions[$mfOption['alias']][$mfOptVal['value']] = $count;
                }
            }
        }
        
        return [
            'suggestions'=>$suggestions,
            'options'=>$options,
        ];
    }
    public function getOptions($category_id){
        $this->pdo->setConfig([
            'class'=>'mfOption',
            'where'=>[
                'mfOption.active'=>1,
            ],
            'select'=>[
                'mfOption'=>'*',
            ],
            'return'=>'data',
            'limit'=>0,
            ]);
        $mfOptions = $this->pdo->run();
        $count = 0;
        foreach($mfOptions as &$mfOption){
            $this->pdo->setConfig([
                'class'=>'mfOptVal',
                'innerJoin'=>[
                    'mfResVal'=>[
                        'class'=>'mfResVal',
                        'on'=>'mfResVal.value_id=mfOptVal.id',
                    ],
                    'mfCategoryChild'=>[
                        'class'=>'mfCategoryChild',
                        'on'=>'mfCategoryChild.resource_id=mfResVal.resource_id',
                    ]
                ],
                'where'=>[
                    'mfOptVal.option_id'=>$mfOption['id'],
                    'mfOptVal.value:!='=>'',
                    'mfCategoryChild.category_id'=>$category_id,
                ],
                'groupby'=>'mfOptVal.id',
                'select'=>[
                    'mfOptVal'=>'*',
                ],
                'return'=>'data',
                'limit'=>0,
                ]);
            $mfOption['mfOptVals'] = $this->pdo->run();
            
            $mfOption['count_val'] = count($mfOption['mfOptVals']);
            $count += $mfOption['count_val'];
        }
        //$this->addTime("getOptions ".$this->pdo->getTime());
        return [
            'count' => $count,
            'mfOptions'=>$mfOptions,
        ];
    }

    public function filter($data = array()){
        $this->addTime('filter init');
        $filters = $this->prepareRequest($data);
        $pdo = $this->getPDOFilter($filters,$data['category_id']);
        $pdo['select'] = [
            'modResource'=>'*',
            'Data'=>$this->modx->getSelectColumns('msProductData', 'Data', '', ['id'], true),
        ];
        $pdo['innerJoin']['modResource'] = [
            'class'=>'modResource',
            'on'=>"modResource.id = mfCategoryChild.resource_id",
        ];
        $pdo['innerJoin']['Data'] = [
            'class'=>'msProductData',
            'on'=>"Data.id = mfCategoryChild.resource_id",
        ];
        $pdo['sortby'] = [
            'modResource.menuindex'=>'ASC',
        ];
        $pdo['limit'] = 10;
        $this->pdo->setConfig($pdo);
        $resourses = $this->pdo->run();
        $this->addTime('filter getResources');
        $results = [];
        foreach ($resourses as $row){
            $results[] = $this->pdo->getChunk($this->config['tpl'], $row);
        }

        $options = $this->getOptions($data['category_id']);
        $this->addTime('filter getOptions');
        if($this->config['suggestions']){
            if(!isset($data['suggestions'])){
                $suggestions = $this->getSuggestions($filters,$options,$data['category_id'])['suggestions'];
                $this->addTime('getFilter getSuggestions');
            }
        }
        if($this->config['suggestions2']){
            if(!isset($data['suggestions'])){
                $suggestions = $this->getSuggestions2($filters,$options,$data['category_id'])['suggestions'];
                $this->addTime('getFilter getSuggestions');
            }
        }
        return $this->success([
            'results'=>implode("\n",$results),
            'suggestions'=>$suggestions,
            'log'=>$this->getTime(),
        ]);
    }
    
    public function prepareRequest($data = array()){
        $this->pdo->setConfig([
            'class'=>'mfOption',
            'where'=>[
                'mfOption.active'=>1,
            ],
            'select'=>[
                'mfOption'=>'*',
            ],
            'return'=>'data',
            'limit'=>0,
            ]);
        $mfOptions = $this->pdo->run();
        $filters = [];
        foreach($mfOptions as $mfOption){
            if($data[$mfOption['alias']]){
                $values = explode(";",$data[$mfOption['alias']]);
                foreach($values as $value){
                    $this->pdo->setConfig([
                        'class'=>'mfOptVal',
                        'where'=>[
                            'mfOptVal.value'=>$value,
                            'mfOptVal.option_id'=>$mfOption['id'],
                        ],
                        'select'=>[
                            'mfOptVal'=>'*',
                        ],
                        'return'=>'data',
                        'limit'=>1,
                        ]);
                    $mfOptVals = $this->pdo->run();
                    if(is_array($mfOptVals) and count($mfOptVals) == 1){
                        $filters[$mfOption['alias']][$mfOptVals[0]['id']] = $mfOptVals[0]['id'];
                    }
                }
            }
        }
        return $filters;
    }
    public function handleRequest($action, $data = array())
    {
        
        set_time_limit(3000);
        $data = $this->modx->sanitize($data, $this->modx->sanitizePatterns);

        switch($action){
            // case 'gen_map_options':
            //     return $this->gen_map_options($data);
            // break;
            
            // case 'update_options':
            //     return $this->update_options($data);
            // break;

            case 'filter':
                return $this->filter($data);
            break;
            case 'get_options':
                return $this->get_options($data);
            break;
            case 'get_ms_val_and_resource_options':
                return $this->get_ms_val_and_resource_options($data);
            break;
            default:
                return $this->error("Not found action $action! {$data['trs_data'][0]['id']}".print_r($data,1));
        }
    }
    public function success($data = [], $message = ""){
        return array('success'=>1,'message'=>$message,'data'=>$data);
    }
    public function error($message = "",$data = []){
        return array('success'=>0,'message'=>$message,'data'=>$data);
    }
}