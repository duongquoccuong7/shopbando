<?php
$filter = filterData('GET');
$user_id = getSession('user_id');

if (!empty($filter)) {
    $favor_id = $filter['id'];
    $check = getOne("SELECT * FROM favorites WHERE id = $favor_id");
    if (!empty($check)) {
        $deleteStatus = delete('favorites', "id = $favor_id");
        if ($deleteStatus) {
            setSessionFlash('msg', 'Product deleted successfully.');
            redirect('?module=favorite&action=index&id=' . $user_id);
        }
    } else {
        setSessionFlash('msg', 'Product does not exist.');
        setSessionFlash('msg_type', 'danger');
    }
} else {
    setSessionFlash('msg', 'An error occurred. Please try again later.');
    setSessionFlash('msg_type', 'danger');
}
