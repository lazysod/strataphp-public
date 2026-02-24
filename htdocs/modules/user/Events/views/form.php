<!-- events/form.php -->
<h1>Create or Edit Event</h1>
<form method="post">
    <label for="title">Title:</label><br>
    <input type="text" id="title" name="title" required><br><br>
    <label for="content">Content:</label><br>
    <textarea id="content" name="content" rows="6" cols="50" required></textarea><br><br>
    <label for="status">Status:</label><br>
    <select id="status" name="status">
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select><br><br>
    <button type="submit">Save Event</button>
</form>
