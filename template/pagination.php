<?php
function makePagination($num_records, $records_per_page, $page, $pgname)
{
    $total_pages = ceil($num_records / $records_per_page); // Calculate the total number of pages

    if ($total_pages > 1) {
        echo "<nav aria-label='Pagination' class='mt-5'>
            <ul class='pagination justify-content-center'>";

        if ($page > 1) {
            echo "<li class='page-item'>
                <a href='$pgname?page=" . ($page - 1) . "' class='page-link'>
                    <i class='bi bi-arrow-left'></i>
                </a>
            </li>";
        }

        $max_pages = 5; // Define the maximum number of pages to display
        $start_page = max($page - floor($max_pages / 2), 1); // Calculate the start page number
        $end_page = min($start_page + $max_pages - 1, $total_pages); // Calculate the end page number

        $start_page = max($end_page - $max_pages + 1, 1); // Adjust the start page if the end page exceeds the total pages

        if ($start_page > 1) {
            echo "<li class='page-item'><a class='page-link' href='$pgname?page=1'>1</a></li>
                <li class='page-item disabled'><span class='page-link'>...</span></li>";
        }

        for ($i = $start_page; $i <= $end_page; $i++) {
            echo "<li class='page-item" . ($page == $i ? " active" : "") . "'>
                <a class='page-link' href='$pgname?page=$i'>$i</a>
            </li>";
        }

        if ($end_page < $total_pages) {
            echo "<li class='page-item disabled'><span class='page-link'>...</span></li>
                <li class='page-item'><a class='page-link' href='$pgname?page=$total_pages'>$total_pages</a></li>";
        }

        if ($page * $records_per_page < $num_records) {
            echo "<li class='page-item'>
                <a href='$pgname?page=" . ($page + 1) . "' class='page-link'>
                    <i class='bi bi-arrow-right'></i>
                </a>
            </li>";
        }

        echo "</ul>
            </nav>";
    }
}
?>