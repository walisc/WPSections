<?php

namespace wpSections\Core\wpsModels;

abstract class wpsBaseModel{

    protected $sectionId = "";
    protected $configTitle = "";
    protected $showInMenu = "";
    protected $dependencies = null;
    protected $wpOOW = null;
    protected $mainModel = null;

    function __construct($sectionId, $wpOOW, $dependencies, $configTitle="", $showInMenu=false)
    {
        $this->sectionId = $sectionId;
        $this->configTitle = $configTitle == "" ? $sectionId : $configTitle;
        $this->showInMenu = $showInMenu;
        $this->dependencies = $dependencies;
        $this->wpOOW = $wpOOW;
    }

    //Mainly for backward comparability. Allows you to override to previous id's
    function GetModelId(){
        return $this->sectionId ;
    }

    function GetModelUrlId(){
        return $this->GetModelId();
    }

    function GetModelMenuName(){
        return $this->configTitle;
    }

    function GetShowInMenu(){
        return $this->showInMenu;
    }

    abstract function GetModelPage();

}