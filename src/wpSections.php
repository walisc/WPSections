<?php

namespace wpSections;

include "wpOOWCustomElements/PageSelector/PageSelector.php";
include "ConfigPages/MenuConfig.php";
include "ConfigPages/PageConfig.php";
include "ConfigPages/StaticPages.php";


class wpSections{

    private $wpOOW = null;
    private $srcPath = "";
    private $exclude = [];
    private $loadedModels = null;
    private $sectionBaseMenu = null;
    private $pageTemplates = [];

    //TODO: \wp_write_post
    //TODO: Consider autoloading wpOOW
    //TODO: Show use that src_path relative to template directory
    function __construct($wpConfig, $srcPath, $wpOOW=null, $exclude = []) {

        $this->wpOOW = $wpOOW;
        $this->srcPath= $srcPath;
        $this->exclude= $exclude;
        
        $this->sectionBaseMenu = $this->wpOOW->CreateMenu($wpConfig->id,
                                                $wpConfig->title,
                                                $wpConfig->wpoowPermission,
                                                $wpConfig->mainView ,
                                                $wpConfig->icon,
                                                $wpConfig->menuPosition);

        add_filter( 'page_template', [$this, "LoadPageTemplate"] );

    }

    public function LoadPageTemplate($page_template){

        $pagename = get_query_var('pagename');
        
        $templateBase = sprintf("%s%s%s%s", __DIR__, DIRECTORY_SEPARATOR, "PageTemplates", DIRECTORY_SEPARATOR);
        if (array_key_exists($pagename, $this->pageTemplates )){
            if ($this->pageTemplates[$pagename] == "Links"){
                $page_template = sprintf("%s%s", $templateBase, "page-links.php");
            }
            else if ($this->pageTemplates[$pagename] == "Rows"){
                $page_template = sprintf("%s%s", $templateBase, "page-rows.php");
            }

        }
        return $page_template;
    }

    public function Load(){
        $this->loadedModels = $this->LoadControllers();
        $this->LoadConfigurationPages();
        $GLOBALS["WP_SECTIONS_LOADED_CONTROLLERS"] =   $this->loadedModels;
        $GLOBALS["WP_SECTIONS_GLOBAL_VARIABLES"] =   [
            "PAGE_TEMPLATES" => $this->pageTemplates 
        ];
        $this->sectionBaseMenu->render();
    }

    private function LoadConfigurationPages(){
        
        $viewableConfigurationSections = []; 
        
        foreach ($this->loadedModels as $sectionModelId => $sectionModel){
            
            if ($sectionModel->HasSectionUserView() == wpsSectionTypes::$CONFIG_VIEW)
            {
                $configurationModel = $sectionModel->GetSectionConfigModel();
                $this->sectionBaseMenu->AddChild($configurationModel->GetConfigModelPage());

                $viewableConfigurationSections[$sectionModel->GetSectionId()] = [
                    "id" => $sectionModel->GetSectionId(),
                    "name" => $sectionModel->GetSectionName(),
                    "description" => $sectionModel->GetSectionDescription(),
                    "configureLink" => sprintf("%s/wp-admin/edit.php?post_type=%s", get_site_url(), $configurationModel->GetConfigModelUrlId()),
                    "type" => "configured" //static
                ];
            }
            elseif ($sectionModel->HasSectionUserView()){

                $viewableConfigurationSections[$sectionModel->GetSectionId()] = [
                    "id" => $sectionModel->GetSectionId(),
                    "name" => $sectionModel->GetSectionName(),
                    "description" => $sectionModel->GetSectionDescription(),
                    "configureLink" => "",
                    "type" => "configured" //static
                ];
            }
            elseif ($sectionModel->GetType() == wpsSectionTypes::$CONFIG){
                $configurationModel = $sectionModel->GetSectionConfigModel();
                $this->sectionBaseMenu->AddChild($configurationModel->GetConfigModelPage());
            }
            
        }

        $static_pages_config = CreateStaticPages($this->wpOOW);

        foreach ($static_pages_config->Query()->Select()->Fetch() as $row)
        {

            $viewableConfigurationSections[$row["_other_pages_id"]] = [
                "id" => $row["_other_pages_id"],
                "name" => $row["_other_pages_name"],
                "description" => "",
                "configureLink" => "",
                "type" => "static"
            ];

        }
        
        $page_configuration = CreatePageConfig($this->wpOOW, $viewableConfigurationSections);
        $menu_config = CreateMenuConfig($this->wpOOW, $page_configuration, $viewableConfigurationSections);

        foreach ($page_configuration->Query()->Select()->Fetch() as $row)
        {
            if ($row["_pages_id"] != null){
                $this->pageTemplates[$row["_pages_id"]] = $row["_pages_display_as"];
            }


        }

        $this->sectionBaseMenu->AddChild($page_configuration);
        $this->sectionBaseMenu->AddChild($static_pages_config);
        $this->sectionBaseMenu->AddChild($menu_config);

    }

    private function LoadControllers(){
        $loaded_sections = [];

        $controllerPath = get_template_directory(). $this->srcPath  . "controllers";

        foreach (array_diff(scandir($controllerPath), array('..', '.')) as $controllerFile )
        {
            $controllerFileName = str_replace(".controller.php", "",$controllerFile);
            $loaded_sections = $this->LoadController($controllerFileName, $controllerPath, $loaded_sections);

        }

        return $loaded_sections;
    }


    private function LoadController($controllerFileName, $controllerPath, $loaded_sections)
    {
        if (!in_array($controllerFileName, $this->exclude) && !array_key_exists($controllerFileName, $loaded_sections) ) {

            $controllerDependencies = [];

            $controllerClass = sprintf("%sController", str_replace("_", "", ucwords($controllerFileName, "_") ));

            include sprintf("%s%s%s.controller.php", $controllerPath, DIRECTORY_SEPARATOR, $controllerFileName);

            $loadedClass = new $controllerClass($controllerFileName, $this->srcPath, $this->wpOOW);
            
            foreach ($loadedClass->GetDependencies() as $dependency)
            {
                $loaded_sections = $this->LoadController($dependency, $controllerPath, $loaded_sections); //TODO: DO this better
                $controllerDependencies[$dependency] = $loaded_sections[$dependency];
            }

            $loadedClass->Load($controllerDependencies);

            $loaded_sections[$controllerFileName] = $loadedClass;
        }
        return $loaded_sections;
    }
}

class wpSectionsConfig{

    public $id;
    public $title;
    public $wpoowPermission;
    public $mainView;
    public $icon;
    public $menuPosition;

    function __construct($id, $title, $wpoowPermission, $mainView, $icon, $menuPosition){
        $this->id = $id;
        $this->title = $title;
        $this->wpoowPermission = $wpoowPermission;
        $this->mainView = $mainView;
        $this->icon = $icon;
        $this->menuPosition = $menuPosition;
    }

}