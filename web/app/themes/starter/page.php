<?php

the_post();

$page = app()->schema('page')->model(\get_the_id());

if (method_exists($page, 'haveBlocks')) {
    if ($page->builder()->hasGridEnabled()) {
        $page->builder()->setGrid();
        echo '<div class="idetik-pagebuilder">';
    }
    while ($page->haveBlocks()) {
        $page->getTheBlock();
    }
    if ($page->builder()->hasGridEnabled()) {
        echo '</div>';
    }
}
