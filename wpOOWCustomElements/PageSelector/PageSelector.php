<?php

class PageSelector extends BaseElement{

    protected $availableChoices = [];
    function __construct($id, $label="", $availableChoices, $permissions=[], $elementPath = '', $elementCssClasses=[])
    {
        parent::__construct($id, $label, $permissions, $elementPath, $elementCssClasses);
        $this->availableChoices = $availableChoices;
        
    }

    function BaseScriptsToLoad(){
        $pluginURLPath = wpAPIUtilities::GetWpAPUriLocation(__DIR__);

        $this->EnqueueElementBaseCSS("wpOOWCusPageSelector", sprintf("%s%s%s", $pluginURLPath, DIRECTORY_SEPARATOR, "PageSelector.css"),  [], []);
        $this->EnqueueElementBaseScript("wpOOWCusPageSelectorJQuerySortable",  sprintf("%s%s%s", $pluginURLPath, DIRECTORY_SEPARATOR, "jquery-sortable.js"),  [], ["jquery"], "1.0.0", true);
        $this->EnqueueElementBaseScript("wpOOWCusPageSelector", sprintf("%s%s%s", $pluginURLPath, DIRECTORY_SEPARATOR, "PageSelector.js"),  [], ["wpOOWCusPageSelectorJQuerySortable"], "1.0.0", true);

    }
    function ReadView($post){
        $selectedChoices = [];

        $selected_value = $this->GetDatabaseValue($post);
        $saved_values = json_decode($selected_value);

        if ($saved_values) {
            foreach ($saved_values as $value) //might need to reverse this
            {
                if (array_key_exists($value, $this->availableChoices )) {
                    array_push($selectedChoices, $this->availableChoices[$value]);
                }
            }
        }

        echo $this->twigTemplate->render('/read_view.mustache', ["id" => $this->id,  "selectedChoices"=>$selectedChoices]);

        $this->EnqueueElementScript("/PageSelector.element.js",  ["id" => $this->id, "enabled"=>"disable"], "wpOOWCusPageSelector");
    }

    function EditView($post){
        parent::EditView($post);

        $selectedChoices = [];

        $selected_value = $this->GetDatabaseValue($post);
        $saved_values = json_decode($selected_value);

        if ($saved_values) {
            foreach ($saved_values as $value) //might need to reverse this
            {
                if (array_key_exists($value, $this->availableChoices )) {
                    array_push($selectedChoices, $this->availableChoices[$value]);
                    unset($this->availableChoices[$value] );
                }
            }
        }

        echo $this->twigTemplate->render('/edit_view.mustache', ["id" => $this->id,  "availableChoices"=>$this->availableChoices, "selectedChoices"=>$selectedChoices, "value"=>$selected_value]);

        $this->EnqueueElementScript("/PageSelector.element.js",  ["id" => $this->id, "enabled"=>"enabled"], "wpOOWCusPageSelector");

    }

    function ProcessPostData($post_id)
    {
        parent::ProcessPostData($post_id);
        $data = sanitize_text_field($_POST[$this->id]);

        $this->SaveElementData($post_id, $data);

    }

}
