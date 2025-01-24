<div class="navigation">
    <div class="scroll-wrapper">
        <button class="toggle-button" onclick="toggleScroll()">°°°</button>
        <div class="scroll-container" id="scroll-container">
            <div class="scroll-content">
                <ul>
                    <li><a href="etudia.php">Ajouter un Etudiant</a></li>
                    <li><a href="notabs.php">Ajouter Les Notes et Les abssences</a></li>
                    <li><a href="etaff.php">Afficher Les Etudiants</a></li>
                    <li><a href="affichprof.php">Afficher Les Notes et Les Absences</a></li>
                    <li><a href="logout.php">Logout</a></li>
            </div>
            <?php if (!empty($_SESSION['loginMessage'])) : ?>
                <span class="login-message"><?php echo $_SESSION['loginMessage']; ?></span>
                <?php unset($_SESSION['loginMessage']); ?>
            <?php endif; ?>
            </ul>
            <script>
                function toggleScroll() {
                    const scrollContainer = document.getElementById('scroll-container');
                    scrollContainer.classList.toggle('closed'); // Ajoute ou enlève la classe 'closed'
                }
            </script>
        </div>
