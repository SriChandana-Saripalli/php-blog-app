<?php if ($total_pages > 1): ?>
<style>
    .pagination {
        margin-top: 40px; /* Increased space between posts and pagination */
        text-align: center;
    }
    .pagination a {
        margin: 0 8px; /* Space between buttons */
        padding: 6px 12px;
        background: rgb(230, 6, 51);
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        display: inline-block;
    }
    .pagination a:hover {
        background: #b00333;
    }
    .pagination a.active {
        background: #333;
    }
</style>
<div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search ?? ''); ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>
<?php endif; ?>