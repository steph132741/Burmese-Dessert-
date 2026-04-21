<?php 
require_once __DIR__ . '/auth.php'; 
require_admin(); 

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    return trim($value, '-') ?: 'product';
}

function handle_product_image_upload(array $file, string $fallbackName): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
        throw new RuntimeException('The selected image could not be uploaded.');
    }

    $originalName = (string)($file['name'] ?? '');
    $originalExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? (string)finfo_file($finfo, $file['tmp_name']) : '';
    if ($finfo) {
        finfo_close($finfo);
    }
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
    ];

    $extension = $allowed[$mime] ?? '';
    if ($extension === '' && in_array($originalExtension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true)) {
        $extension = $originalExtension === 'jpeg' ? 'jpg' : $originalExtension;
    }

    if ($extension === '') {
        throw new RuntimeException('Please select a valid image file: JPG, PNG, WEBP, GIF, or SVG.');
    }

    $uploadDir = __DIR__ . '/../assets/img/uploads';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
        throw new RuntimeException('The upload folder could not be created.');
    }

    $fileName = slugify($fallbackName) . '-' . bin2hex(random_bytes(6)) . '.' . $extension;
    $targetPath = $uploadDir . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('The image could not be saved. Please try again.');
    }

    return 'assets/img/uploads/' . $fileName;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = db()->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: ' . asset_url('admin/products.php'));
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$saved = isset($_GET['saved']);

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
    $hasUploadError = false;
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'short_description' => trim($_POST['short_description'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price' => (float)($_POST['price'] ?? 0),
        'image' => $product['image'],
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'stock' => (int)($_POST['stock'] ?? 0),
    ];

    if ($data['slug'] === '') {
        $data['slug'] = slugify($data['name']);
    }

    try {
        $uploadedImage = handle_product_image_upload($_FILES['image'] ?? [], $data['slug'] ?: $data['name']);
        if ($uploadedImage !== null) {
            $data['image'] = $uploadedImage;
        }
    } catch (Throwable $e) {
        $hasUploadError = true;
        set_flash('error', $e->getMessage());
        $product = array_merge($product, $data);
    }

    if ($data['image'] === '') {
        $data['image'] = 'assets/img/mont-let-saung.svg';
    }

    if (!$hasUploadError) {
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
            set_flash('success', 'Product information successfully updated.');
            header('Location: ' . asset_url('admin/product_edit.php') . '?id=' . $id . '&saved=1');
            exit;
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
            $newId = (int)db()->lastInsertId();
            set_flash('success', 'Product created and live on the shop now.');
            header('Location: ' . asset_url('admin/product_edit.php') . '?id=' . $newId . '&saved=1');
            exit;
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $id ? 'Edit' : 'Add' ?> Product</title>
<link rel="stylesheet" href="<?= asset_url('assets/css/styles.css') ?>">
</head>

<body>

<main class="section">

<div class="banner">
<h2><?= $id ? 'Edit' : 'Add' ?> Product</h2>
<?php if ($id): ?>
<p><a href="<?= asset_url('product.php') ?>?id=<?= (int)$id ?>" target="_blank">Open customer product page</a></p>
<?php endif; ?>
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
<div class="image-upload-row">
<label class="btn btn-secondary btn-file" for="image">Choose Image</label>
<input type="file" id="image" name="image" accept="image/*">
</div>

<?php if (!empty($product['image'])): ?>
<p><?= $saved ? 'Updated Image:' : 'Current Image:' ?></p>
<img id="product-image-preview" src="<?= htmlspecialchars(product_image_url($product['image'])) ?>" width="160" style="border-radius:12px;object-fit:cover;">
<?php else: ?>
<p>Preview after selecting a file:</p>
<img id="product-image-preview" src="<?= htmlspecialchars(product_image_url('')) ?>" width="160" style="border-radius:12px;object-fit:cover;">
<?php endif; ?>
</div>

<div class="form-group">
<label for="stock">Stock</label>
<input id="stock" name="stock" type="number" value="<?= htmlspecialchars($product['stock']) ?>" required>
</div>

<div class="form-group">
<div class="featured-row">
<label class="featured-inline" for="is_featured">
<input id="is_featured" type="checkbox" name="is_featured" <?= $product['is_featured'] ? 'checked' : '' ?>>
<span>Featured</span>
</label>
<p class="field-help">Show this product on the shop’s featured section.</p>
</div>
</div>

<div class="hero-cta">
<button class="btn btn-primary" type="submit">Save</button>
<a class="btn btn-secondary" href="<?= asset_url('admin/products.php') ?>">Cancel</a>
</div>

</form>

</main>

<script src="<?= asset_url('assets/js/app.js') ?>"></script>
</body>
</html>
