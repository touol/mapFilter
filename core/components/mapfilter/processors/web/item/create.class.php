<?php

class mapFilterOfficeItemCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'mapFilterItem';
    public $classKey = 'mapFilterItem';
    public $languageTopics = ['mapfilter'];
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('name'));
        if (empty($name)) {
            $this->modx->error->addField('name', $this->modx->lexicon('mapfilter_item_err_name'));
        } elseif ($this->modx->getCount($this->classKey, ['name' => $name])) {
            $this->modx->error->addField('name', $this->modx->lexicon('mapfilter_item_err_ae'));
        }

        return parent::beforeSet();
    }

}

return 'mapFilterOfficeItemCreateProcessor';