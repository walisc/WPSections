<?php

namespace wpSections\ConfigPages;

function CreateStaticPages($wpOOW){
    $static_pages_config = $wpOOW->CreatePostType("_static_pages", "Static Sections", true);

    $static_pages_config->AddField(new Text("_other_pages_id", "Page Id", [wpAPIPermissions::AddPage => "", wpAPIPermissions::EditPage => "r"]));
    $static_pages_config->AddField(new Text("_other_pages_name", "Section Name"));
    $static_pages_config->AddField(new RichTextArea("_other_pages_content", "Content"));

    $static_pages_config->RegisterBeforeSaveEvent("CreateStaticID");
    
    return $static_pages_config;
}

function CreateStaticID($data)
{
    $data["_other_pages_id"] = sanitize_title($data[sprintf("%s_%s", "_static_pages", "_other_pages_name")]);
    return $data;
}