<?php
include_once dirname(__FILE__) . '/update.class.php';
class mapFilterItemDisableProcessor extends mapFilterItemUpdateProcessor
{
    public function beforeSet()
    {
        $this->setProperty('active', false);
        return true;
    }
}
return 'mapFilterItemDisableProcessor';
