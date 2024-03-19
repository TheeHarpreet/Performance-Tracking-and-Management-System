<div class="header-container">
    <header>
        <div class="top-header">
            <a href="index.php"><img class="logo"src="images/logo.png" alt="MIROS Logo"></a>
        </div>
        <div class="bottom-header">
            <?php
            if (basename($_SERVER['PHP_SELF']) == "index.php") {
                echo "<p>Welcome, $user->fname!</p>";
            }
            ?>
            <a href="login.php">logout</a>
        </div>
    </header>
</div>