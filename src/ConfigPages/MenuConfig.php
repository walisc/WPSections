<?php

function CreateMenuConfig($wpOOW, $page_configuration, $viewableConfigurationSections){
    // Static Pages
    $available_pages = [];
    $available_sections = ["" => ""]; //TODO: To Filter this dependant on page

    foreach ($page_configuration->Query()->Select()->Fetch() as $row)
    {
        $available_pages[$row["_pages_id"]] = $row["_pages_name"];

    }

    foreach ($viewableConfigurationSections as $key => $value)
    {
        $available_sections[$key] = $value["name"];

    }
    $menu_config = $wpOOW->CreatePostType("_menu_config", "Menu Config", true);

    $menu_config->AddField(new Text("_menu_config_level_1", "Top Level Text"));
    $menu_config->AddField(new Text("_menu_config_level_2", "Second Level Text"));
    $menu_config->AddField(new Text("_menu_config_order", "Order"));
    $menu_config->AddField(new Select("_menu_config_page", "Page to Navigate", $available_pages));
    $menu_config->AddField(new Select("_menu_config_section", "Section to Anchor (need to be on the page)", $available_sections));
    $menu_config->AddField(new Text("_menu_config_external_link", "External Link (Overides Page Selection)"));
    $menu_config->AddField(new Checkbox("_menu_config_is_action_button", "Is Action Button"));
    $menu_config->AddField(new Checkbox("_menu_config_is_enabled", "Is Enabled"));

    $menu_config->RegisterBeforeDataFetch("OrderMenus");

    return $menu_config;
}

function OrderMenus($query){

    $query->set('order', 'ASC');
    $query->set('orderby', 'meta_value_num');
    $query->set('meta_key', '_menu_config__menu_config_order_value_key'); //TODO: Hack right way is to use postKey->GetFieldDbKey(); need to think of a way of passing that if not object
    return $query;

}
