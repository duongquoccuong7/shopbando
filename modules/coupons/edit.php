<?php
layout('/dashboard/header', 'Edit Coupon');

$getData = filterData('GET');
$data_id = isset($getData['id']) ? (int)$getData['id'] : 0; // Cast to integer to prevent SQL Injection

// Safe query
$Data = getOne("SELECT * FROM coupons WHERE id=$data_id");

if (!$Data) {
    setSessionFlash('msg', 'Coupon does not exist.');
    setSessionFlash('msg_type', 'red');
    redirect('?module=coupons&action=index');
}

if (isPost()) {
    $filter = filterData();
    $errors = [];
    $max_dis = 0;
    $dis_v = 0;

    // Clean numerical data (remove commas added by JS)
    $discount_value_raw = (float)str_replace(',', '', $filter['discount_value'] ?? 0);
    $min_order_raw      = (float)str_replace(',', '', $filter['min_order'] ?? 0);
    $max_discount_raw   = (float)str_replace(',', '', $filter['max_discount'] ?? 0);

    // Validation
    if (empty($filter['description'])) {
        $errors['description']['required'] = 'Please enter a description';
    }
    if (empty($filter['name'])) {
        $errors['name']['required'] = 'Please enter the program name';
    }
    if (empty($filter['code'])) {
        $errors['code']['required'] = 'Please enter the promo code';
    }
    if (empty($filter['start'])) {
        $errors['start']['required'] = 'Please select a start date';
    }
    if (empty($filter['end'])) {
        $errors['end']['required'] = 'Please select an end date';
    }
    if (empty($filter['quantity'])) {
        $errors['quantity']['required'] = 'Please enter the quantity';
    }
    if (empty($filter['discount_value'])) {
        $errors['discount_value']['required'] = 'Please enter the discount value';
    }
    if (empty($filter['min_order'])) {
        $errors['min_order']['required'] = 'Please enter the minimum order amount';
    }
    if ($filter['type'] == '1' && empty($filter['max_discount'])) {
        $errors['max_discount']['required'] = 'Please enter the maximum discount amount';
    }

    if (empty($errors)) {
        if ($filter['type'] == '0') { // Fixed amount discount
            $max_dis = $discount_value_raw * 1000;
            $dis_v   = $discount_value_raw * 1000;
        } else if ($filter['type'] == '1') { // Percentage discount
            $max_dis = $max_discount_raw * 1000;
            $dis_v   = $discount_value_raw;
        }

        $dataupdate = [
            'name'           => $filter['name'],
            'description'    => $filter['description'],
            'slug'           => $filter['slug'],
            'code'           => $filter['code'],
            'type'           => $filter['type'],
            'discount_value' => $dis_v,
            'min_order'      => $min_order_raw * 1000,
            'max_discount'   => $max_dis,
            'quantity'       => $filter['quantity'],
            'start'          => $filter['start'],
            'end'            => $filter['end'],
            'updated_at'     => date('Y-m-d H:i:s'),
            'status'         => $filter['status']
        ];

        $updatestatus = update('coupons', $dataupdate, $data_id);
        if ($updatestatus) {
            setSessionFlash('msg', 'Coupon updated successfully.');
            setSessionFlash('msg_type', 'green');
            redirect('?module=coupons&action=index');
        } else {
            setSessionFlash('msg', 'Failed to update coupon.');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        setSessionFlash('msg', 'Update failed, please check your input.');
        setSessionFlash('msg_type', 'red');
        setSessionFlash('old_data', $filter);
        setSessionFlash('errors', $errors);
    }
}

// Retrieve flash session values
$msg       = getSessionFlash('msg');
$msg_type  = getSessionFlash('msg_type');
$olddata   = getSessionFlash('old_data');
$errorsArr = getSessionFlash('errors');

// Prioritize submitted old data if validation failed, otherwise load DB record
if (empty($olddata)) {
    $olddata = $Data;
}
?>

<!-- START MAIN VIEW -->
<div class="main-wrap">
    <?php layout('/dashboard/sidebar'); ?>

    <div class="content-menu">
        <?php if (!empty($msg) && !empty($msg_type)) : ?>
            <div class="anoun-mess">
                <?php getMess($msg, $msg_type); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="add-product">
                <!-- Product Left -->
                <div class="add-product-left">
                    <!-- Coupon Name -->
                    <div class="product-input">
                        <label for="name" class="label-input">
                            Program Name
                            <?php if (!empty($errorsArr['name'])): ?>
                                - <?php echo formError($errorsArr, 'name'); ?>
                            <?php endif; ?>
                        </label><br>
                        <input type="text" id="name" name="name" placeholder="Name..."
                            value="<?php echo htmlspecialchars(oldData($olddata, 'name')); ?>">
                    </div>

                    <!-- Code -->
                    <div class="product-input">
                        <label for="code" class="label-input">
                            Promo Code
                            <?php if (!empty($errorsArr['code'])): ?>
                                - <?php echo formError($errorsArr, 'code'); ?>
                            <?php endif; ?>
                        </label><br>
                        <input type="text" id="code" name="code" placeholder="Enter code..."
                            value="<?php echo htmlspecialchars(oldData($olddata, 'code')); ?>">
                    </div>

                    <!-- Quantity -->
                    <div class="product-input">
                        <label for="quantity" class="label-input">
                            Coupon Quantity
                            <?php if (!empty($errorsArr['quantity'])): ?>
                                - <?php echo formError($errorsArr, 'quantity'); ?>
                            <?php endif; ?>
                        </label><br>
                        <input type="text" id="quantity" name="quantity" placeholder="Quantity..."
                            value="<?php echo htmlspecialchars(oldData($olddata, 'quantity')); ?>">
                    </div>

                    <!-- Slug -->
                    <div class="product-input">
                        <label for="slug" class="label-input">URL Slug</label><br>
                        <input type="text" id="slug" name="slug" placeholder="URL Slug..."
                            value="<?php echo htmlspecialchars(oldData($olddata, 'slug')); ?>">
                    </div>

                    <!-- Description -->
                    <div class="product-input">
                        <label for="description" class="label-input">
                            Description
                            <?php if (!empty($errorsArr['description'])): ?>
                                - <?php echo formError($errorsArr, 'description'); ?>
                            <?php endif; ?>
                        </label><br>
                        <textarea class="text-edit"
                            style="width:100%; height:150px; resize:none; font-size:1.4rem; padding:10px"
                            id="description" name="description"
                            placeholder="Description..."><?php echo htmlspecialchars(oldData($olddata, 'description')); ?></textarea>
                    </div>
                </div>

                <!-- Product Right -->
                <div class="add-product-right">
                    <!-- Start Date -->
                    <div class="product-input">
                        <label for="start" class="label-input">
                            Start Date
                            <?php if (!empty($errorsArr['start'])): ?>
                                - <?php echo formError($errorsArr, 'start'); ?>
                            <?php endif; ?>
                        </label><br>
                        <input type="date" id="start" name="start"
                            value="<?php echo !empty($olddata['start']) ? date('Y-m-d', strtotime($olddata['start'])) : ''; ?>">
                    </div>

                    <!-- End Date -->
                    <div class="product-input">
                        <label for="end" class="label-input">
                            End Date
                            <?php if (!empty($errorsArr['end'])): ?>
                                - <?php echo formError($errorsArr, 'end'); ?>
                            <?php endif; ?>
                        </label><br>
                        <input type="date" id="end" name="end"
                            value="<?php echo !empty($olddata['end']) ? date('Y-m-d', strtotime($olddata['end'])) : ''; ?>">
                    </div>

                    <!-- Discount Type -->
                    <div class="product-input">
                        <label for="type" class="label-input">Discount Type</label><br>
                        <select name="type" class="edit-select" id="type">
                            <option value="0"
                                <?php echo (isset($olddata['type']) && $olddata['type'] == 0) ? 'selected' : ''; ?>>
                                Fixed Amount</option>
                            <option value="1"
                                <?php echo (isset($olddata['type']) && $olddata['type'] == 1) ? 'selected' : ''; ?>>
                                Percentage (%)</option>
                        </select>
                    </div>

                    <!-- Minimum Order Amount -->
                    <div class="product-input">
                        <label for="min_order" class="label-input">
                            Minimum Order Value
                            <?php if (!empty($errorsArr['min_order'])): ?>
                                - <?php echo formError($errorsArr, 'min_order'); ?>
                            <?php endif; ?>
                        </label><br>
                        <input type="text" id="min_order" name="min_order" placeholder="Minimum order value..."
                            value="<?php echo htmlspecialchars(oldData($olddata, 'min_order')); ?>">
                    </div>

                    <!-- Discount Value -->
                    <div class="product-input">
                        <label for="discount_value" class="label-input">
                            Discount Value
                            <?php if (!empty($errorsArr['discount_value'])): ?>
                                - <?php echo formError($errorsArr, 'discount_value'); ?>
                            <?php endif; ?>
                        </label><br>
                        <input type="text" id="discount_value" name="discount_value" placeholder="Discount value..."
                            value="<?php echo htmlspecialchars(oldData($olddata, 'discount_value')); ?>">
                    </div>

                    <!-- Maximum Discount -->
                    <div class="product-input">
                        <label for="max_discount" class="label-input">
                            Maximum Discount Value
                            <?php if (!empty($errorsArr['max_discount'])): ?>
                                - <?php echo formError($errorsArr, 'max_discount'); ?>
                            <?php endif; ?>
                        </label><br>
                        <input type="text" id="max_discount" name="max_discount"
                            placeholder="Discount % or max amount..."
                            value="<?php echo htmlspecialchars(oldData($olddata, 'max_discount')); ?>">
                    </div>

                    <!-- Status -->
                    <div class="product-input">
                        <label for="status" class="label-input">Status</label><br>
                        <select name="status" class="edit-select" id="status">
                            <option value="1"
                                <?php echo (isset($olddata['status']) && $olddata['status'] == 1) ? 'selected' : ''; ?>>
                                Active</option>
                            <option value="0"
                                <?php echo (isset($olddata['status']) && $olddata['status'] == 0) ? 'selected' : ''; ?>>
                                Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">Submit</button>
        </form>
    </div>
</div>

<script>
    // Automatically generate slug
    function createSlug(str) {
        return str.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    document.getElementById('name').addEventListener('input', function() {
        document.getElementById('slug').value = createSlug(this.value);
    });

    // Automatically format numbers with thousand separators
    const inputs = [
        document.getElementById('discount_value'),
        document.getElementById('min_order'),
        document.getElementById('max_discount')
    ];

    inputs.forEach(input => {
        if (input) {
            input.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            });
        }
    });
</script>

<?php layout('/dashboard/footer'); ?>