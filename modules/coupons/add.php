<?php
layout('/dashboard/header', 'Add Coupon');
if (isPost()) {
    $filter = filterData();
    $errors = [];
    $max_dis = 0;
    $dis_v = 0;

    // Validate description 
    if (empty($filter['description'])) {
        $errors['description']['required'] = 'Please enter a description.';
    }

    // Validate name
    if (empty($filter['name'])) {
        $errors['name']['required'] = 'Please enter a program name.';
    }

    // Validate code
    if (empty($filter['code'])) {
        $errors['code']['required'] = 'Please enter a coupon code.';
    }
    // else {
    //     if (strlen(trim($filter['code'])) != 5) {
    //         $errors['code']['required'] = 'Coupon code must be exactly 5 characters.';
    //     }
    // }

    // Validate start date
    if (empty($filter['start'])) {
        $errors['start']['required'] = 'Please select a start date.';
    }
    // else {
    //     if (!empty($filter['start'] > date('Y-m-d'))) {
    //         $errors['start']['start_date'] = 'Start date cannot be earlier than today.';
    //     }
    // }

    // Validate end date
    if (empty($filter['end'])) {
        $errors['end']['required'] = 'Please select an end date.';
    }
    // else {
    //     if (!empty(($filter['end'] < $filter['start']))) {
    //         $errors['end']['end_date'] = 'End date cannot be earlier than start date.';
    //     }
    // }

    // Validate quantity
    if (empty($filter['quantity'])) {
        $errors['quantity']['required'] = 'Please enter the quantity.';
    }

    // Validate discount_value
    if (empty($filter['discount_value'])) {
        $errors['discount_value']['required'] = 'Please enter the discount value.';
    }

    // Validate min_order
    if (empty($filter['min_order'])) {
        $errors['min_order']['required'] = 'Please enter the minimum order value.';
    }

    // Validate max_discount
    if ($filter['type'] == '1' && empty($filter['max_discount'])) {
        $errors['max_discount']['required'] = 'Please enter the maximum discount amount.';
    }

    if (empty($errors)) {
        // Insert data
        if ($filter['type'] == '0') {
            $max_dis = $filter['discount_value'] * 1000;
            $dis_v = $filter['discount_value'] * 1000;
        } else if ($filter['type'] == '1') {
            $max_dis = $filter['max_discount'] * 1000;
            $dis_v = $filter['discount_value'];
        }

        $datainsert = [
            'name' => $filter['name'],
            'description' => $filter['description'],
            'slug' => $filter['slug'],
            'code' => $filter['code'],
            'type' => $filter['type'],
            'discount_value' => $dis_v,
            'min_order' => $filter['min_order'] * 1000,
            'max_discount' => $max_dis,
            'quantity' => $filter['quantity'],
            'start' => $filter['start'],
            'end' => $filter['end'],
            'created_at' => date('Y:m:d H:i:s'),
            'status' => 1
        ];

        $insertstatus = insert('coupons', $datainsert);
        if ($insertstatus) {
            setSessionFlash('msg', 'Coupon added successfully.');
            setSessionFlash('msg_type', 'green');
            redirect('?module=coupons&action=add');
        } else {
            setSessionFlash('msg', 'Failed to add coupon.');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        setSessionFlash('msg', 'Failed to add coupon, please check your input data.');
        setSessionFlash('msg_type', 'red');
        setSessionFlash('old_data', $filter);
        setSessionFlash('errors', $errors);
    }
}

$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$olddata = getSessionFlash('old_data');
$errorsArr = getSessionFlash('errors');
?>
<!-- start main -->
<div class="main-wrap">
    <?php
    layout('/dashboard/sidebar');
    ?>
    <!-- Add Product Coupon -->
    <div class="content-menu">
        <?php if (!empty($msg) && !empty($msg_type)) : ?>
            <div class="anoun-mess">
                <?php getMess($msg, $msg_type); ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="add-product">
                <!-- product left -->
                <div class="add-product-left">
                    <!-- Program Name -->
                    <div class="product-input">
                        <label for="name" class="label-input">
                            <?php
                            echo 'Program Name';
                            if (!empty($errorsArr['name'])) {
                                echo ' - ' . formError($errorsArr, 'name');
                            }
                            ?>
                        </label><br>
                        <input type="text" id="name" name="name" placeholder="Name..." value="<?php
                                                                                                if (!empty($olddata['name'])) {
                                                                                                    echo oldData($olddata, 'name');
                                                                                                }   ?>">
                    </div>

                    <!-- Coupon Code -->
                    <div class="product-input">
                        <label for="code" class="label-input">
                            <?php
                            echo 'Coupon Code';
                            if (!empty($errorsArr['code'])) {
                                echo ' - ' . formError($errorsArr, 'code');
                            }
                            ?>
                        </label><br>
                        <input type="text" id="code" name="code" placeholder="Enter code..." value="<?php
                                                                                                    if (!empty($olddata['code'])) {
                                                                                                        echo oldData($olddata, 'code');
                                                                                                    }   ?>">
                    </div>

                    <!-- Coupon Quantity -->
                    <div class="product-input">
                        <label for="quantity" class="label-input">
                            <?php
                            echo 'Quantity';
                            if (!empty($errorsArr['quantity'])) {
                                echo ' - ' . formError($errorsArr, 'quantity');
                            }
                            ?>
                        </label><br>
                        <input type="text" id="quantity" name="quantity" placeholder="Quantity..." value="<?php
                                                                                                            if (!empty($olddata['quantity'])) {
                                                                                                                echo oldData($olddata, 'quantity');
                                                                                                            }   ?>">
                    </div>

                    <!-- Slug -->
                    <div class="product-input">
                        <label for="slug" class="label-input">Slug</label><br>
                        <input type="text" id="slug" name="slug" placeholder="Slug..." value="<?php
                                                                                                if (!empty($olddata['slug'])) {
                                                                                                    echo oldData($olddata, 'slug');
                                                                                                }   ?>">
                    </div>

                    <!-- Description -->
                    <div class="product-input">
                        <label for="description" class="label-input">
                            <?php
                            echo 'Description';
                            if (!empty($errorsArr['description'])) {
                                echo ' - ' . formError($errorsArr, 'description');
                            }
                            ?>
                        </label><br>
                        <textarea class="text-edit"
                            style="width:100%; height:150px; resize:none; font-size:1.4rem; padding:10px"
                            id="description" name="description" placeholder="Description..."><?php
                                                                                                if (!empty($olddata['description'])) {
                                                                                                    echo oldData($olddata, 'description');
                                                                                                } ?></textarea>
                    </div>
                </div>

                <!-- product right -->
                <div class="add-product-right">
                    <!-- Start Date -->
                    <div class="product-input">
                        <label for="start" class="label-input">
                            <?php
                            echo 'Start Date';
                            if (!empty($errorsArr['start'])) {
                                echo ' - ' . formError($errorsArr, 'start');
                            }
                            ?>
                        </label><br>
                        <input style="width: 130px" type="date" id="start" name="start" value="<?php
                                                                                                if (!empty($olddata['start'])) {
                                                                                                    echo oldData($olddata, 'start');
                                                                                                }   ?>">
                    </div>

                    <!-- End Date -->
                    <div class="product-input">
                        <label for="end" class="label-input">
                            <?php
                            echo 'End Date';
                            if (!empty($errorsArr['end'])) {
                                echo ' - ' . formError($errorsArr, 'end');
                            }
                            ?>
                        </label><br>
                        <input style="width: 130px" type="date" id="end" name="end" value="<?php
                                                                                            if (!empty($olddata['end'])) {
                                                                                                echo oldData($olddata, 'end');
                                                                                            }   ?>">
                    </div>

                    <!-- Discount Type -->
                    <div class="product-input">
                        <label for="type" class="label-input">Discount Type</label><br>
                        <select name="type" class="edit-select" id="type">
                            <option value="0">Fixed Amount</option>
                            <option value="1">Percentage (%)</option>
                        </select>
                    </div>

                    <!-- Minimum Order Amount -->
                    <div class="product-input">
                        <label for="min_order" class="label-input">
                            <?php
                            echo 'Minimum Order Amount';
                            if (!empty($errorsArr['min_order'])) {
                                echo ' - ' . formError($errorsArr, 'min_order');
                            }
                            ?>
                        </label><br>
                        <input type="text" id="min_order" name="min_order" placeholder="Minimum order value..."
                            value="<?php
                                    if (!empty($olddata['min_order'])) {
                                        echo oldData($olddata, 'min_order');
                                    }   ?>">
                    </div>

                    <!-- Discount Value -->
                    <div class="product-input">
                        <label for="discount_value" class="label-input">
                            <?php
                            echo 'Discount Value';
                            if (!empty($errorsArr['discount_value'])) {
                                echo ' - ' . formError($errorsArr, 'discount_value');
                            }
                            ?>
                        </label><br>
                        <input type="text" id="discount_value" name="discount_value" placeholder="Discount value..."
                            value="<?php
                                    if (!empty($olddata['discount_value'])) {
                                        echo oldData($olddata, 'discount_value');
                                    }   ?>">
                    </div>

                    <!-- Maximum Discount Amount -->
                    <div class="product-input">
                        <label for="max_discount" class="label-input">
                            <?php
                            echo 'Maximum Discount Amount';
                            if (!empty($errorsArr['max_discount'])) {
                                echo ' - ' . formError($errorsArr, 'max_discount');
                            }
                            ?>
                        </label><br>
                        <input type="text" id="max_discount" name="max_discount" placeholder="Max discount..."
                            value="<?php
                                    if (
                                        !empty($olddata['type']) &&
                                        $olddata['type'] == '1' &&
                                        !empty($olddata['max_discount'])
                                    ) {
                                        echo oldData($olddata, 'max_discount');
                                    }
                                    ?>">
                    </div>
                </div>
            </div>
            <button onclick="sendData()" type="submit" class="btn-submit">
                Submit
            </button>
        </form>
    </div>
    <!-- end content-menu -->
</div>
<!-- end main -->

<script>
    // Converts input string to a URL-friendly slug
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
        const getValue = this.value;
        document.getElementById('slug').value = createSlug(getValue);
    });

    // Formatting currency/number inputs with commas
    const inputs = [
        document.getElementById('discount_value'),
        document.getElementById('min_order'),
        document.getElementById('max_discount')
    ];

    inputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        });
    });
</script>

<?php
layout('/dashboard/footer');
?>