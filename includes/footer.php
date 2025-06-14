<?php
// includes/footer.php
// Close the database connection ONLY HERE after all operations are done for the page.
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
        </div> </main> <footer class="footer mt-auto py-4 bg-dark text-white-50">
        <div class="container text-center">
            <p class="mb-0">© <?php echo date('Y'); ?> Miru. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>