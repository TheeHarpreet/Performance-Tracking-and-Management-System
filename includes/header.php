<div class="header-container">
    <header>
        <div class="top-header">
            <a href="index.php"><img class="logo"src="images/logo.png" alt="MIROS Logo"></a>
        </div>
        <div class="bottom-header">
            <?php
            if (basename($_SERVER['PHP_SELF']) == "index.php") { // Only outputs the name on the index page.
                echo "<p>Hello, $user->fname $user->lname!</p>"; // NeedsTranslation
            }
            ?>
            <a href="login.php" class="logout-button-header">Logout</a> <!-- NeedsTranslation -->
            <?php 
            echo "
            <div class='language'>
            <form method='post' class='translate-container'>
                <button type='submit' name='lang' value='en' class='translate-en'>EN</button>
                <button type='submit' name='lang' value='bm' class='translate-bm'>BM</button>
            </form>
            </div>
            ";
            ?>
        </div>
    </header>
</div>