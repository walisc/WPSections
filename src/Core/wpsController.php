<?php

namespace wpSections\Core;

abstract class wpsController{

    protected $sectionType = "";
    protected $id = "";
    protected $srcPath = "";
    protected $wpOOW = null;
    protected $dependencies = [];
    protected $configModel = null;
    protected $hasUerView = false;
    
    function __construct($controllerIdName, $srcPath, $wpOOW){
        $this->id = $controllerIdName;
        $this->srcPath = $srcPath;
        $this->wpOOW = $wpOOW;
        $this->sectionType = $this->GetType();
        $this->configModels = [];
        $this->userViewsPaths = array_merge(glob(sprintf("%sviews%s%s.view.php",  $this->srcPath, DIRECTORY_SEPARATOR, $this->id)),
                                            glob(sprintf("%sviews%s%s.*.view.php",  $this->srcPath, DIRECTORY_SEPARATOR, $this->id)));
        $this->userConfigModelsPaths = array_merge(glob( sprintf("%s%sconfigModels%s%s.configmodel.php", get_template_directory(), $this->srcPath, DIRECTORY_SEPARATOR, $this->id)),
                                                   glob( sprintf("%s%sconfigModels%s%s.*.configmodel.php", get_template_directory(), $this->srcPath, DIRECTORY_SEPARATOR, $this->id)));
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

        if ($this->HasConfigModel()) {

            foreach ($this->userConfigModelsPaths as $userConfigModelsPath){

                //Convert and name to it class equivalent i.e schedule_details.user.configmodel.php -> ScheduleDetailsUserConfigModel
                $modelClass = sprintf("%sConfigModel", str_replace("_","", ucwords(str_replace(".","_", str_replace(".php", "", basename($userConfigModelsPath))), "_")));

                include $userConfigModelsPath;

                $showInMenu = false; //$this->sectionType == wpsSectionTypes::$CONFIG;
                array_push($this->configModels, new $modelClass($this->GetSectionId(), $this->wpOOW, $this->dependencies, $this->GetSectionName(), $showInMenu));
            }
           
        }
    }

    function GetSectionConfigModels(){
        return $this->configModels;
    }

    // A section can only have one view
    // TODO: Think of way to pass variables
    function GetSectionUserView($view=null, $paramenter=[] ){

        if ($this->HasSectionUserView()) {

            foreach ($this->userViewsPaths as $userViewPaths){
                if ($view == null) {
                    if ($userViewPaths == sprintf("%sviews%s%s.view.php", $this->srcPath, DIRECTORY_SEPARATOR, $this->id)) {
                        get_template_part(sprintf("%sviews%s%s.view", $this->srcPath, DIRECTORY_SEPARATOR, $this->id));
                        break;
                    }
                }
                else{
                    if ($userViewPaths == sprintf("%sviews%s%s.view.%s.php",  $this->srcPath, DIRECTORY_SEPARATOR, $this->id, $view))
                    {
                        get_template_part(sprintf("%sviews%s%s.view.%s.php",  $this->srcPath, DIRECTORY_SEPARATOR, $this->id, $view));
                        break;
                    }
                }
            }
        }
    }

    function HasSectionUserView(){
        return count($this->userViewsPaths) > 0;
    }

    function HasConfigModel(){
        return count($this->userConfigModelsPaths) > 0;
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
    public static $TOOL = "TOOL";
    public static $SETTING = "SETTING";
}