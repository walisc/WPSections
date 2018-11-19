<?php


namespace wpSections\Core\wpsModels;

abstract class wpsConfigModel extends wpsBaseModel{

    protected $sectionId = "";
    protected $configTitle = "";
    protected $showInMenu = "";
    protected $dependencies = null;
    protected $wpOOW = null;
    protected $mainModel = null;

    function __construct($sectionId, $wpOOW, $dependencies, $configTitle="", $showInMenu=false)
    {
        parent::__construct( $sectionId, $wpOOW, $dependencies, $configTitle, $showInMenu);
        $this->mainModel = $this->wpOOW->CreatePostType($this->GetConfigModelId(), $this->GetConfigModelMenuName() , $this->ShouldPersist(), ["show_in_menu" => $this->GetShowInMenu()]);
    }

    function GetUniqueFieldId($fieldName)
    {
        return sprintf("%s_%s", $this->sectionId, $fieldName);
    }


    function ShouldPersist(){
        return  true;
    }


    
}