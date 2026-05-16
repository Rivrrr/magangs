<nav class="mt-4 d-flex justify-content-between align-items-center">
    <span class="text-muted" style="font-size: 13px;">
        Menampilkan <?= $start_data ?> hingga <?= $end_data ?> dari <?= $total_data ?> entri
    </span>

    <ul class="pagination pagination-sm m-0">
        <li class="page-item <?= ($page_active <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page_active - 1 ?>">Sebelumnya</a>
        </li>

        <?php for ($i = 1; $i <= $total_page; $i++) : ?>
            <li class="page-item <?= ($i == $page_active) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= ($page_active >= $total_page) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page_active + 1 ?>">Selanjutnya</a>
        </li>
    </ul>
</nav>