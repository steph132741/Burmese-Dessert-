<?php 
require_once __DIR__ . '/auth.php'; 
require_admin(); 

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = db()->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: /burmese-desserts/admin/products.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$product = [
    'name' => '',
    'slug' => '',
    'short_description' => '',
    'description' => '',
    'price' => '',
    'image' => '',
    'is_featured' => 0,
    'stock' => 50,
];

if ($id) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        $product = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $imagePath = $product['image'];

    if (!empty($_FILES['image']['name'])) {

        $uploadDir = __DIR__ . '/../assets/img/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = 'assets/img/' . $fileName;
        }
    }

    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'short_description' => trim($_POST['short_description'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price' => (float)($_POST['price'] ?? 0),
        'image' => $imagePath,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'stock' => (int)($_POST['stock'] ?? 0),
    ];

    if ($id) {
        $stmt = db()->prepare('UPDATE products 
        SET name = ?, slug = ?, short_description = ?, description = ?, price = ?, image = ?, is_featured = ?, stock = ? 
        WHERE id = ?');

        $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['short_description'],
            $data['description'],
            $data['price'],
            $data['image'],
            $data['is_featured'],
            $data['stock'],
            $id
        ]);
    } else {

        $stmt = db()->prepare('INSERT INTO products 
        (name, slug, short_description, description, price, image, is_featured, stock) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)');

        $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['short_description'],
            $data['description'],
            $data['price'],
            $data['image'],
            $data['is_featured'],
            $data['stock']
        ]);
    }

    header('Location: /burmese-desserts/admin/products.php');
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $id ? 'Edit' : 'Add' ?> Product</title>
<link rel="stylesheet" href="/burmese-desserts/assets/css/styles.css">
</head>

<body>

<main class="section">

<div class="banner">
<h2><?= $id ? 'Edit' : 'Add' ?> Product</h2>
</div>

<form method="post" enctype="multipart/form-data" class="hero-card" style="max-width:720px;">

<div class="form-group">
<label for="name">Name</label>
<input id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
</div>

<div class="form-group">
<label for="slug">Slug</label>
<input id="slug" name="slug" value="<?= htmlspecialchars($product['slug']) ?>" required>
</div>

<div class="form-group">
<label for="short_description">Short Description</label>
<input id="short_description" name="short_description" value="<?= htmlspecialchars($product['short_description']) ?>" required>
</div>

<div class="form-group">
<label for="description">Description</label>
<textarea id="description" name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>
</div>

<div class="form-group">
<label for="price">Price (Ks)</label>
<input id="price" name="price" type="number" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>
</div>

<div class="form-group">
<label for="image">Product Image</label>
<input type="file" id="image" name="image" accept="image/*">

<?php if (!empty($product['image'])): ?>
<p>Current Image:</p>
<img src="/burmese-desserts/<?= htmlspecialchars($product['image']) ?>" width="120">
<?php endif; ?>
</div>

<div class="form-group">
<label for="stock">Stock</label>
<input id="stock" name="stock" type="number" value="<?= htmlspecialchars($product['stock']) ?>" required>
</div>

<div class="form-group">
<label>
<input type="checkbox" name="is_featured" <?= $product['is_featured'] ? 'checked' : '' ?>>
Featured
</label>
</div>

<button class="btn btn-primary" type="submit">Save</button>
<a class="btn btn-secondary" href="/burmese-desserts/admin/products.php">Cancel</a>

</form>

</main>

</body>
</html>