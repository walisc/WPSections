<?php


abstract class wpsController{

    protected $sectionType = "";
    protected $id = "";
    protected $srcPath = "";
    protected $wpOOW = null;
    protected $dependencies = [];
    protected $configModel = null;
    
    function __construct($controllerIdName, $srcPath, $wpOOW){
        $this->id = $controllerIdName;
        $this->srcPath = $srcPath;
        $this->wpOOW = $wpOOW;
        $this->sectionType = $this->GetType();
    }

    abstract function GetType();
    abstract function GetSectionName();
    abstract function GetSectionDescription();

    final function GetSectionId(){
        return $this->id;
    }

    function GetDependencies()
    {
        return [];
    }
    
    //A section only has one model, or though this can be a combination of other, look at dependacies
    function LoadSectionConfigModel(){

        if ($this->sectionType == wpsSectionTypes::$CONFIG || $this->sectionType == wpsSectionTypes::$CONFIG_VIEW) {
            $modelClass = sprintf("%sConfigModel", str_replace("_", "", ucwords($this->id, "_") ));

            include sprintf("%s%sconfigModels%s%s.configmodel.php", get_template_directory(), $this->srcPath, DIRECTORY_SEPARATOR, $this->id);

            $showInMenu = false; //$this->sectionType == wpsSectionTypes::$CONFIG;
            $this->configModel = new $modelClass($this->GetSectionId(), $this->wpOOW, $this->dependencies, $this->GetSectionName(), $showInMenu);
        }
    }

    function GetSectionConfigModel(){
        return $this->configModel;
    }

    // A section can only have one view
    // TODO: Think of way to pass variables
    function GetSectionView(){

        if ($this->sectionType == wpsSectionTypes::$VIEW || $this->sectionType == wpsSectionTypes::$CONFIG_VIEW) {
            get_template_part(sprintf("%sviews%s%s.view",  $this->srcPath, DIRECTORY_SEPARATOR, $this->id));
        }

    }

    function ShowInMenu(){
        return false;
    }
    
    function Load($dependencies){
        $this->dependencies = $dependencies;
        $this->LoadSectionConfigModel();
    }
}

class wpsSectionTypes{
    public static $CONFIG = "CONFIG";
    public static $VIEW = "VIEW";
    public static $CONFIG_VIEW = "CONFIG_VIEW";
}