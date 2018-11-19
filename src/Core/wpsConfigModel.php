<?php

abstract class wpsConfigModel{

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

        $this->mainModel = $this->wpOOW->CreatePostType($this->GetConfigModelId(), $this->GetConfigModelMenuName() , $this->ShouldPersist(), ["show_in_menu" => $this->GetShowInMenu()]);
    }

    function GetUniqueFieldId($fieldName)
    {
        return sprintf("%s_%s", $this->sectionId, $fieldName);
    }

    //Mainly for backward comparability. Allows you to override to previous id's
    function GetConfigModelId(){
        return $this->sectionId ;
    }

    function GetConfigModelUrlId(){
        return $this->GetConfigModelId();
    }

    function GetConfigModelMenuName(){
        return $this->configTitle;
    }

    function GetShowInMenu(){
        return $this->showInMenu;
    }

    function ShouldPersist(){
        return  true;
    }

    abstract function GetConfigModelPage();
    
}