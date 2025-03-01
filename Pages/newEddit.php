<?php
    include "../Blocks/header.php";
?>
<div class="new-Eddit">
    <form method="post" action="/functions/forEddits/newEddit.php" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <label for="image" class="custom-file-upload">Select Picture</label>
        <input type="file" id="image" name="image[]" multiple>
        <div class="button-container">
            <button type="submit">Create</button>
        </div>
    </form>
</div>

