<?php

function CreatePageConfig($wpOOW, $viewableConfigurationSections){

    $page_configuration = $wpOOW->CreatePostType("_page_config", "Page Layout & Config", true); //TODO ID cant be too long

    $page_configuration->AddField(new Text("_pages_id", "Page Id", [wpAPIPermissions::AddPage => "", wpAPIPermissions::EditPage => "r"]));
    $page_configuration->AddField(new Text("_pages_name", "Page Name"));
    $page_configuration->AddField(new Select("_pages_display_as", "Display As", ["Rows" => "Rows", "Links" => "Links"]));
    $page_configuration->AddField(new Checkbox("_pages_is_home_page", "Is Home Page"));
    $page_configuration->AddField(new PageSelector("_pages_page_selector", "Section Selector", $viewableConfigurationSections));
    //$page_configuration->AddField(new RichTextArea("_pages_page_preview", "Preview", [wpAPIPermissions::AddPage => "", wpAPIPermissions::EditPage => ""]));

    $page_configuration->RegisterBeforeSaveEvent("CreateConfigID");
    $page_configuration->RegisterAfterSaveEvent("AddPageType");
    return $page_configuration;

}

function CreateConfigID($data)
{
    $data["_pages_id"] = sanitize_title($data[sprintf("%s_%s", "_page_config", "_pages_name")]);
    $data["_pages_page_preview"] = sprintf('<a href="%s/%s/"> Preview</a>', get_site_url(), $data["_pages_id"] );
    return $data;
}

function AddPageType($data){
    if( null == get_page_by_title( $data["_pages_id"] ) ) {
        $post_id = wp_insert_post(
            array(
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_title' => $data["_pages_id"],
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
                'page_template' => 'page-faq.php'
            )
        );
    }
}