<?php
$filter = filterData('GET');
if (!empty($filter)) {
    $category_id = $filter['id'];
    $check = getOne("SELECT * FROM genders WHERE id = $category_id");
    if (!empty($check)) {
        $deleteStatus = delete('genders', "id = $category_id");
        if ($deleteStatus) {
            setSessionFlash('msg', 'Gender deleted successfully.');
            setSessionFlash('msg_type', 'green');
            redirect('?module=gender&action=index');
        }
    } else {
        setSessionFlash('msg', 'Failed to delete gender.');
        setSessionFlash('msg_type', 'red');
    }
} else {
    setSessionFlash('msg', 'Gender could not be deleted. Please try again.');
    setSessionFlash('msg_type', 'red');
}