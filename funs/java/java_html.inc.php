<?php


function java_html_header($for_edit = false)
{
    echo "<!doctype html>\n";
    echo "<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:th=\"http://www.thymeleaf.org\">\n";

    if (!$for_edit) {
        echo "<head th:replace=\"_header_inc::admin_header\"></head>\n";

    } else {
        echo "<head th:replace=\"_header_inc::admin_header_for_edit\"></head>\n";
    }

    echo "<body class=\"bg-light\">\n";
    echo "<nav th:replace=\"_header_inc::admin_nav\"></nav>\n";
    echo _tab(1) . "<div class=\"d-flex\">\n";
    echo _tab(1) . "<div th:replace=\"_header_inc::admin_menu\"></div>\n";
    echo _tab(1) . "<div class=\"content p-4\">\n";

}

function java_html_footer1($for_edit = false)
{
    echo _tab(1) . "</div>\n";
    echo "</div>\n";
    if (!$for_edit) {
        echo "<div th:replace=\"_footer_inc::admin_footer\"></div>\n";
    } else {
        echo "<div th:replace=\"_footer_inc::admin_footer_for_edit\"></div>\n";
    }

}


function java_html_footer2()
{

    echo "</body>\n";
    echo "</html>\n";
}

function _java_html_op_url_param($key, $var, $ii)
{
    $join = ($ii == 1) ? "" : ",";
    return "{$join}{$key}=\${{$var}.{$key}}";
}