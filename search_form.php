<style>
.search-box {
    margin-bottom: 20px;
    text-align: center;
}
.search-box form {
    display: inline-flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: center;
}
.search-box input[type="text"] {
    width: 300px;
    padding: 8px 12px;
    font-size: 14px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
.search-box button {
    margin-top: 6px;
    padding: 8px 16px;
    background: rgb(230, 6, 51);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.search-box button:hover {
    background: #b00333;
}
</style>
<div class="search-box">
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search posts..." 
               value="<?php echo htmlspecialchars($search ?? ''); ?>" />
        <button type="submit">Search</button>
    </form>
</div>