<?php require 'views/layouts/admin_header.php'; ?>
<style>
.tab-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.tab-btn { padding: 0.6rem 1.5rem; border-radius: 8px; border: 1px solid #334155; background: #1e293b; color: #cbd5e1; cursor: pointer; font-weight: 600; text-decoration: none; transition: all 0.2s; }
.tab-btn.active, .tab-btn:hover { background: #f59e0b; color: #0f172a; border-color: #f59e0b; }
.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; margin-top: 1rem; background: #1e293b; border-radius: 8px; overflow: hidden; }
th, td { padding: 0.85rem 1rem; text-align: left; border-bottom: 1px solid #334155; color: #cbd5e1; font-size: 0.9rem; }
th { background: #0f172a; font-weight: 600; color: #94a3b8; text-transform: uppercase; font-size: 0.8rem; }
.action-btns { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); overflow-y: auto; }
.modal-content { background: #1e293b; color: white; margin: 5% auto; padding: 2rem; border: 1px solid #334155; width: 90%; max-width: 600px; border-radius: 12px; position: relative; }
.modal-content h2 { margin-bottom: 1.5rem; color: #f59e0b; }
.close { color: #64748b; float: right; font-size: 28px; font-weight: bold; cursor: pointer; line-height: 1; }
.close:hover { color: white; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; margin-bottom: 0.4rem; color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
.form-group .form-control { width: 100%; padding: 0.6rem 0.9rem; border-radius: 8px; border: 1px solid #334155; background: #0f172a; color: white; font-size: 0.95rem; box-sizing: border-box; }
.form-group textarea.form-control { resize: vertical; }
.low-stock { color: #ef4444; font-weight: bold; }
.variant-row { background: rgba(0,0,0,0.3); padding: 0.75rem; border-radius: 8px; margin-bottom: 0.5rem; border: 1px solid #334155; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
.variant-add-row { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: flex-end; }
</style>

<?php
$filter = $_GET['filter'] ?? 'all';
$allProducts = $data['products'];
$categories = $data['categories'];
$categoryMap = [];
foreach ($categories as $cat) $categoryMap[$cat['category_id']] = $cat['name'];

// Low stock products
$lowStockProducts = array_filter($allProducts, fn($p) => ($p['stock'] ?? 0) <= 5);

if ($filter === 'lowstock') $displayProducts = $lowStockProducts;
elseif ($filter === 'category' && isset($_GET['cat_id'])) {
    $catId = (int)$_GET['cat_id'];
    $displayProducts = array_filter($allProducts, fn($p) => ($p['category_id'] ?? 0) == $catId);
} else $displayProducts = $allProducts;
?>

<div class="admin-dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2>Manage Products</h2>
            <p style="color:#64748b; margin-top:0.3rem;">Total: <?= count($allProducts) ?> | Low Stock: <span style="color:#ef4444;"><?= count($lowStockProducts) ?></span></p>
        </div>
        <button class="tab-btn active" onclick="openAddModal()">+ Add Product</button>
    </div>

    <div class="tab-bar">
        <a href="/admin/products?filter=all" class="tab-btn <?= $filter === 'all' ? 'active' : '' ?>">All Products</a>
        <a href="/admin/products?filter=lowstock" class="tab-btn <?= $filter === 'lowstock' ? 'active' : '' ?>" style="<?= $filter === 'lowstock' ? '' : 'border-color:#ef4444; color:#ef4444;' ?>">⚠ Low Stock (≤5)</a>
        <!-- Category filter dropdown -->
        <div style="position:relative; display:inline-block;">
            <select id="catFilter" onchange="if(this.value) window.location='/admin/products?filter=category&cat_id='+this.value;"
                style="padding:0.6rem 1.5rem; border-radius:8px; border:1px solid #334155; background:#1e293b; color:#cbd5e1; cursor:pointer; font-weight:600; font-size:0.9rem;">
                <option value="">Filter by Category</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>" <?= (isset($_GET['cat_id']) && $_GET['cat_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Size/Variant</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($displayProducts as $product): ?>
                <tr>
                    <td style="color:#64748b;"><?= $product['product_id'] ?></td>
                    <td style="font-weight:bold;"><?= htmlspecialchars($product['name']) ?></td>
                    <td style="color:#64748b;"><?= htmlspecialchars($product['category_name'] ?? '—') ?></td>
                    <td style="color:#a78bfa;"><?= htmlspecialchars($product['variant_key'] ?? 'Default') ?></td>
                    <td style="color:#f59e0b;">Rs <?= number_format($product['price'] ?? 0, 2) ?></td>
                    <td>
                        <?php if(($product['stock'] ?? 0) <= 5): ?>
                            <span class="low-stock">⚠ <?= $product['stock'] ?? 0 ?></span>
                        <?php else: ?>
                            <?= $product['stock'] ?? 0 ?>
                        <?php endif; ?>
                    </td>
                    <td class="action-btns">
                        <button class="tab-btn" style="padding:0.3rem 0.8rem; font-size:0.8rem;" onclick='openEditModal(<?= json_encode($product) ?>)'>✏ Edit</button>
                        <form method="POST" action="/admin/delete_product/<?= $product['product_id'] ?>" style="display:inline;">
                            <button type="submit" style="background:#ef4444; border:none; color:white; padding:5px 12px; border-radius:6px; cursor:pointer; font-size:0.85rem;" onclick="return confirm('Delete this product?');">🗑 Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($displayProducts)): ?>
                <tr><td colspan="7" style="text-align:center; color:#475569; padding:2rem;">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== ADD MODAL ===== -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeAddModal()">&times;</span>
    <h2>Add New Product</h2>
    <form method="POST" action="/admin/add_product" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Premium Cotton Shirt">
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="form-control" required>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Short product description..."></textarea>
        </div>

        <div style="border-top: 1px solid #334155; padding-top: 1rem; margin-top: 1rem; margin-bottom: 1.5rem;">
            <h4 style="color:#a78bfa; margin-bottom:0.8rem; display:flex; justify-content:space-between; align-items:center;">
                <span>Sizes & Variants</span>
                <button type="button" class="tab-btn" style="padding:4px 10px; font-size:0.75rem;" onclick="addVariantRowToAddModal()">+ Add Another Size</button>
            </h4>
            <div id="add_variants_list">
                <div class="variant-row-item" style="display:grid; grid-template-columns: 2fr 2fr 1.5fr 0.5fr; gap:0.5rem; margin-bottom:0.5rem; align-items:center;">
                    <input type="text" name="variant_keys[]" class="form-control" placeholder="e.g. M" value="Default" required 
                           style="padding: 0.4rem 0.6rem; font-size: 0.85rem; background:#0f172a; color:white; border:1px solid #334155; border-radius:6px;">
                    <input type="number" step="0.01" name="variant_prices[]" class="form-control" placeholder="Price (Rs)" required 
                           style="padding: 0.4rem 0.6rem; font-size: 0.85rem; background:#0f172a; color:white; border:1px solid #334155; border-radius:6px;">
                    <input type="number" name="variant_stocks[]" class="form-control" placeholder="Stock" required 
                           style="padding: 0.4rem 0.6rem; font-size: 0.85rem; background:#0f172a; color:white; border:1px solid #334155; border-radius:6px;">
                    <span style="color:#ef4444; font-weight:bold; cursor:pointer; text-align:center; font-size:1.1rem;" onclick="if(document.querySelectorAll('#add_variants_list .variant-row-item').length > 1) this.parentElement.remove(); else alert('Product must have at least one variant.');">×</span>
                </div>
            </div>
            <small style="color:#64748b;">Add multiple sizes (e.g. M, L, XL) and their corresponding prices and stock counts.</small>
        </div>

        <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <small style="color:#64748b;">Leave empty to use default image</small>
        </div>
        <button type="submit" style="background:#f59e0b; border:none; color:#0f172a; padding:0.75rem 2rem; border-radius:8px; cursor:pointer; font-weight:bold; margin-top:0.5rem; width:100%;">Add Product</button>
    </form>
  </div>
</div>

<!-- ===== EDIT MODAL ===== -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeEditModal()">&times;</span>
    <h2>Edit Product</h2>
    <form method="POST" action="/admin/update_product" enctype="multipart/form-data">
        <input type="hidden" name="product_id" id="edit_product_id">
        <input type="hidden" name="variant_id" id="edit_variant_id">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
            <div class="form-group">
                <label>Size / Variant</label>
                <input type="text" name="variant_key" id="edit_variant_key" class="form-control">
            </div>
            <div class="form-group">
                <label>Price (Rs)</label>
                <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock" id="edit_stock" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label>Update Product Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <small style="color:#64748b;">Leave empty to keep existing image</small>
        </div>

        <div style="border-top: 1px solid #334155; padding-top:1.5rem; margin-top:1.5rem; margin-bottom:1.5rem;">
            <h4 style="color:#a78bfa; margin-bottom:1rem; display:flex; justify-content:space-between; align-items:center;">
                <span>Add More Sizes / Variants</span>
                <button type="button" class="tab-btn" style="padding:4px 10px; font-size:0.75rem;" onclick="addVariantRowToEditModal()">+ Add Another Size</button>
            </h4>
            <div id="edit_new_variants_list">
                <!-- Dynamic rows here -->
            </div>
            <small style="color:#64748b;">Add as many sizes and prices as you want to this product.</small>
        </div>

        <button type="submit" style="background:#f59e0b; border:none; color:#0f172a; padding:0.75rem 2rem; border-radius:8px; cursor:pointer; font-weight:bold; margin-top:1rem; width:100%;">Update Product</button>
    </form>
  </div>
</div>

<script>
function openAddModal() { document.getElementById('addModal').style.display = "block"; }
function closeAddModal() { document.getElementById('addModal').style.display = "none"; }
function openEditModal(product) {
    document.getElementById('edit_product_id').value = product.product_id;
    document.getElementById('edit_variant_id').value = product.variant_id || '';
    document.getElementById('edit_name').value = product.name || '';
    document.getElementById('edit_description').value = product.description || '';
    document.getElementById('edit_price').value = product.price || '';
    document.getElementById('edit_stock').value = product.stock || '';
    document.getElementById('edit_variant_key').value = product.variant_key || 'Default';
    
    // Clear dynamic edit variants list
    document.getElementById('edit_new_variants_list').innerHTML = '';
    
    document.getElementById('editModal').style.display = "block";
}
function closeEditModal() { document.getElementById('editModal').style.display = "none"; }

function addVariantRowToAddModal() {
    const list = document.getElementById('add_variants_list');
    const row = document.createElement('div');
    row.className = 'variant-row-item';
    row.style.display = 'grid';
    row.style.gridTemplateColumns = '2fr 2fr 1.5fr 0.5fr';
    row.style.gap = '0.5rem';
    row.style.marginBottom = '0.5rem';
    row.style.alignItems = 'center';
    row.innerHTML = `
        <input type="text" name="variant_keys[]" class="form-control" placeholder="e.g. L" required 
               style="padding: 0.4rem 0.6rem; font-size: 0.85rem; background:#0f172a; color:white; border:1px solid #334155; border-radius:6px;">
        <input type="number" step="0.01" name="variant_prices[]" class="form-control" placeholder="Price (Rs)" required 
               style="padding: 0.4rem 0.6rem; font-size: 0.85rem; background:#0f172a; color:white; border:1px solid #334155; border-radius:6px;">
        <input type="number" name="variant_stocks[]" class="form-control" placeholder="Stock" required 
               style="padding: 0.4rem 0.6rem; font-size: 0.85rem; background:#0f172a; color:white; border:1px solid #334155; border-radius:6px;">
        <span style="color:#ef4444; font-weight:bold; cursor:pointer; text-align:center; font-size:1.1rem;" onclick="this.parentElement.remove()">×</span>
    `;
    list.appendChild(row);
}

function addVariantRowToEditModal() {
    const list = document.getElementById('edit_new_variants_list');
    const row = document.createElement('div');
    row.className = 'variant-row-item';
    row.style.display = 'grid';
    row.style.gridTemplateColumns = '2fr 2fr 1.5fr 0.5fr';
    row.style.gap = '0.5rem';
    row.style.marginBottom = '0.5rem';
    row.style.alignItems = 'center';
    row.innerHTML = `
        <input type="text" name="new_variant_keys[]" class="form-control" placeholder="e.g. XL" required 
               style="padding: 0.4rem 0.6rem; font-size: 0.85rem; background:#0f172a; color:white; border:1px solid #334155; border-radius:6px;">
        <input type="number" step="0.01" name="new_variant_prices[]" class="form-control" placeholder="Price (Rs)" required 
               style="padding: 0.4rem 0.6rem; font-size: 0.85rem; background:#0f172a; color:white; border:1px solid #334155; border-radius:6px;">
        <input type="number" name="new_variant_stocks[]" class="form-control" placeholder="Stock" required 
               style="padding: 0.4rem 0.6rem; font-size: 0.85rem; background:#0f172a; color:white; border:1px solid #334155; border-radius:6px;">
        <span style="color:#ef4444; font-weight:bold; cursor:pointer; text-align:center; font-size:1.1rem;" onclick="this.parentElement.remove()">×</span>
    `;
    list.appendChild(row);
}

window.onclick = function(event) {
    if (event.target == document.getElementById('addModal')) closeAddModal();
    if (event.target == document.getElementById('editModal')) closeEditModal();
}
</script>
<?php require 'views/layouts/admin_footer.php'; ?>
