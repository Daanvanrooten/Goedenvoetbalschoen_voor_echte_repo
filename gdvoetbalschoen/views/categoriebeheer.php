<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categoriebeheer</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <style>
        .cat-container { max-width: 600px; margin: 2rem auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px #eee; padding: 2rem; }
        .cat-title { font-size: 2rem; margin-bottom: 1rem; }
        .cat-form { display: flex; gap: 1rem; margin-bottom: 2rem; }
        .cat-form input { flex: 1; padding: 0.5rem; border-radius: 6px; border: 1px solid #ccc; }
        .cat-form button { padding: 0.5rem 1.5rem; border-radius: 6px; border: none; background: #6b5b95; color: #fff; font-weight: 600; cursor: pointer; }
        .cat-list { margin-top: 1rem; }
        .cat-item { display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #eee; }
        .cat-actions button { margin-left: 0.5rem; border: none; background: #eee; color: #333; border-radius: 4px; padding: 0.3rem 0.8rem; cursor: pointer; }
        .cat-actions button.edit { background: #ffd700; color: #333; }
        .cat-actions button.delete { background: #d32f2f; color: #fff; }
    </style>
</head>
<body>
    <div class="cat-container">
        <a href="admin_dashboard.php" style="display:inline-block;margin-bottom:1rem;padding:0.5rem 1.2rem;background:#6b5b95;color:#fff;border-radius:6px;text-decoration:none;font-weight:600;">Terug naar admin</a>
        <h1 class="cat-title">Categoriebeheer</h1>
        <form class="cat-form" id="addCatForm">
            <input type="text" name="cat_name" id="cat_name" placeholder="Nieuwe categorie..." required>
            <button type="submit">Toevoegen</button>
        </form>
        <div class="cat-list" id="catList">
            <!-- Dynamisch geladen -->
        </div>
    </div>
    <script>
    function fetchCategories() {
        fetch('../phpcode/categorie_api.php?action=list')
            .then(res => res.json())
            .then(data => {
                const catList = document.getElementById('catList');
                catList.innerHTML = '';
                if (data.success && data.categories.length) {
                    data.categories.forEach(cat => {
                        catList.innerHTML += `<div class="cat-item" data-id="${cat.category_id}">
                            <span>${cat.name}</span>
                            <span class="cat-actions">
                                <button class="edit" onclick="editCat(${cat.category_id}, '${cat.name}')">Bewerken</button>
                                <button class="delete" onclick="deleteCat(${cat.category_id})">Verwijderen</button>
                            </span>
                        </div>`;
                    });
                } else {
                    catList.innerHTML = '<p>Geen categorieën gevonden.</p>';
                }
            });
    }
    document.getElementById('addCatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('cat_name').value;
        fetch('../phpcode/categorie_api.php?action=add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'name=' + encodeURIComponent(name)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cat_name').value = '';
                fetchCategories();
            } else {
                alert(data.message);
            }
        });
    });
    window.editCat = function(id, oldName) {
        const newName = prompt('Nieuwe naam voor categorie:', oldName);
        if (newName && newName !== oldName) {
            fetch('../phpcode/categorie_api.php?action=edit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&name=${encodeURIComponent(newName)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) fetchCategories();
                else alert(data.message);
            });
        }
    }
    window.deleteCat = function(id) {
        if (confirm('Weet je zeker dat je deze categorie wilt verwijderen?')) {
            fetch('../phpcode/categorie_api.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) fetchCategories();
                else alert(data.message);
            });
        }
    }
    fetchCategories();
    </script>
</body>
</html>
