<?php

function the_term_title(){
    if (!is_category() && !is_tag() && !is_tax())
        return false;
	
    $term = get_queried_object();
    echo esc_html($term->name);
}