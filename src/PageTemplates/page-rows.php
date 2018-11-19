<?php
/*
Template Name: Wp Sections - Row Layout
*/


get_header(); ?>


<?php
$pagename = get_query_var('pagename');
$tjc_pages = wpAPIObjects::GetInstance()->GetObject("_page_config");
$tjc_static_pages_config = wpAPIObjects::GetInstance()->GetObject("_static_pages");

foreach ($tjc_pages->Query()->Select()->Fetch() as $page)
{
    if ($page["_pages_id"] == $pagename)
    {
        $sections =  json_decode($page["_pages_page_selector"]);

        if (is_array($sections)) {
            foreach ($sections as $section) {
                if (array_key_exists($section, $GLOBALS["WP_SECTIONS_LOADED_CONTROLLERS"])) {
                    $section_details = $GLOBALS["WP_SECTIONS_LOADED_CONTROLLERS"][$section];
                    echo '<div id="'.$section_details->GetSectionId().'" >';
                    $section_details->GetSectionView();
                    echo '</div >';
                }
                else{

                    foreach ($tjc_static_pages_config->Query()->Select()->Fetch() as $row)
                    {
                        if ($row["_other_pages_id"] == $section){
                            echo '<div id="'.$row["_other_pages_id"].'" >';
                            echo html_entity_decode(wpautop($row["_other_pages_content"]));
                            echo '</div >';
                            break;
                        }

                    }
                }
            }

        }
    }
    
}

?>


<?php get_footer(); ?>