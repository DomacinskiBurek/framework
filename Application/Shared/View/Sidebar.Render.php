<?php

$result = static function ($source, $route = '') use (&$result) {
    $content = "";

    foreach ($source as $item) {
        switch (true) {
            case ($item['type'] == "module"):
                if ($item['is_hidden']) break;

                if (count($item['tree']) > 0) {
                    $content .= "
                        <li class=\"nk-menu-heading\">
                            <h6 class=\"overline-title text-primary-alt\">{$item['name']}</h6>
                        </li>
                        <li class=\"nk-menu-item has-sub\">
                    ";

                    $content .= $result($item['tree'], $item['route']);

                    $content .= "</li>";
                } else {
                    $content .= "
                        <li class=\"nk-menu-heading\">
                            <h6 class=\"overline-title text-primary-alt\">{$item['name']}</h6>
                        </li>
                    ";
                }
                break;
            case ($item['type'] == "page"):
                if ($item['is_hidden']) break;

                if (count($item['tree']) > 0) {
                    $content .= "
                     <li class=\"nk-menu-item has-sub\">
                         <a href=\"#\" class=\"nk-menu-link nk-menu-toggle\">
                            <span class=\"nk-menu-icon\"><em class=\"fas {$item['icon']}\"></em></span>
                            <span class=\"nk-menu-text\">{$item['name']}</span>
                        </a>
                        <ul class=\"nk-menu-sub\">
                    ";
                    $content .= $result($item['tree'], "$route" . $item['route']);
                    $content .= "</ul></li>";
                } else {
                    $content .= "
                        <li class=\"nk-menu-item\">
                            <a href=\"$route{$item['route']}\" class=\"nk-menu-link\">
                                <span class=\"nk-menu-icon\"><em class=\"fas {$item['icon']}\"></em></span>
                                <span class=\"nk-menu-text\">{$item['name']}</span>
                            </a>
                        </li>
                    ";
                }
                break;
            case ($item['type'] == "sub_page"):
                if ($item['is_hidden']) break;

                $content .= "
                    <li class=\"nk-menu-item\">
                        <a href=\"$route{$item['route']}\" class=\"nk-menu-link\">
                            <span class=\"nk-menu-text\">{$item['name']}</span>
                        </a>
                    </li>
                ";
                break;
        }
    }

    return $content;
};
?>

<ul class="nk-menu">
    <?=$result((sidebarmenu())->menu_tree);?>
</ul><!-- .nk-menu -->