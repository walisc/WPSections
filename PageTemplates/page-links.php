<?php
/*
Template Name: Wp Sections - Links Layout
*/

$pagename = get_query_var('pagename');
$title = str_replace("-", " ", ucwords($pagename, "-"));
get_header();
?>
    <section class="info_page_header">
        <h1 class="page-name"><?php echo $title ?></h1>
    </section>

<?php

$page_id = "";

if (isset($_GET['vs'])) {
    $page_id = $_GET['vs'];
}




?>

    <section class="other_pages">
        <div class="container">
            <div class="content">
                <div class="col-md-9">
                    <?php
                    $tjc_pages = wpAPIObjects::GetInstance()->GetObject("_page_config");

                    $page_found = false;
                    $first_sections =null;
                    $qlAvailableSections = [];

                    function loadSection($sectionId){
                        if (array_key_exists($sectionId, $GLOBALS["WP_SECTIONS_LOADED_CONTROLLERS"])) {
                            $section_details = $GLOBALS["WP_SECTIONS_LOADED_CONTROLLERS"][$sectionId];
                            $section_details->GetSectionView();

                        }
                        else{
                            $tjc_static_pages_config = wpAPIObjects::GetInstance()->GetObject("_static_pages");

                            foreach ($tjc_static_pages_config->Query()->Select()->Fetch() as $row)
                            {
                                if ($row["_other_pages_id"] == $sectionId){
                                    echo '<div id="'.$row["_other_pages_id"].'" >';
                                    echo html_entity_decode(wpautop($row["_other_pages_content"]));
                                    echo '</div >';
                                    break;
                                }

                            }
                        }
                    }

                    function getSectionLinks($sectionId){
                        global $qlAvailableSections;

                        if (array_key_exists($sectionId, $GLOBALS["WP_SECTIONS_LOADED_CONTROLLERS"])) {
                            $section_details = $GLOBALS["WP_SECTIONS_LOADED_CONTROLLERS"][$sectionId];
                            $qlAvailableSections[$sectionId] = $section_details->GetSectionName();

                        }
                        else{
                            $tjc_static_pages_config = wpAPIObjects::GetInstance()->GetObject("_static_pages");

                            foreach ($tjc_static_pages_config->Query()->Select()->Fetch() as $row)
                            {
                                if ($row["_other_pages_id"] == $sectionId){
                                    $qlAvailableSections[$sectionId] = $row["_other_pages_name"] ;
                                    break;
                                }

                            }
                        }
                    }

                    foreach ($tjc_pages->Query()->Select()->Fetch() as $page)
                    {
                        if ($page["_pages_id"] == $pagename)
                        {
                            $sections =  json_decode($page["_pages_page_selector"]);

                            if (is_array($sections)) {
                                foreach ($sections as $index => $section) {

                                    if ( $page_id == $section)
                                    {
                                        loadSection($section);
                                        $page_found = true;
                                    }

                                    if ($index == 0){
                                        $first_sections = $section;
                                    }
                                    getSectionLinks($section);
                                }
                                if (!$page_found){
                                    loadSection($first_sections);
                                }

                            }
                        }

                    }

                    ?>

                </div>
                <aside class="col-md-3">
                    <h2>Other Links</h2>
                    <?php
                    global $qlAvailableSections;

                    foreach ($qlAvailableSections as $linkId => $linkName){
                        echo '<p><a href="' .sprintf("%s/%s/?vs=%s", get_site_url(), $pagename, $linkId). '">' . $linkName . '</a></p>';
                    }
                    ?>
                </aside>
            </div>
        </div>
    </section>
<?


?>


<?php get_footer(); ?>