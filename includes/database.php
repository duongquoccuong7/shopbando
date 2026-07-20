<?php


// Hàm truy vấn dữ liệu lấy tất cả dữ liệu
function getAll($sql)
{
    global $conn;
    $stm = $conn->prepare($sql);
    $stm->execute();
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

// Hàm truy vấn 1 dòng dữ liệu
function getOne($sql)
{
    global $conn;
    try {
        $stm = $conn->prepare($sql);
        $stm->execute();
        $result = $stm->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;
    } catch (Exception $e) {
        return null;
    }
}

/// Hàm thêm dữ liệu
function insert($table, $data)
{
    global $conn;
    $keys = array_keys($data);
    $cot = implode(',', $keys);
    $place = ':' . implode(',:', $keys);
    $sql = "INSERT INTO $table ($cot) VALUES($place)";
    $stm = $conn->prepare($sql);

    if ($stm->execute($data)) {
        return $conn->lastInsertId(); // Trả về ID vừa tạo luôn, nếu không cần ID thì nó vẫn tương đương giá trị true
    }
    return false;
}

//Hàm đếm số dòng trả về
function getRows($sql)
{
    global $conn;
    $stm = $conn->prepare($sql);
    $stm->execute();
    $rel = $stm->rowCount();
    return $rel;
}

//Hàm cập nhật dữ liệu 
function update($table, $data, $id = '')
{
    global $conn;
    $update = '';
    foreach ($data as $key => $value) {
        $update .= $key . '=:' . $key . ',';
    }
    $update = trim($update, ',');

    if (!empty($id)) {
        // SỬA TẠI ĐÂY: Thêm rõ ràng tên trường "id = :id_condition"
        $sql = "UPDATE $table SET $update WHERE id = :id_condition";
        $data['id_condition'] = $id; // Nạp ID điều kiện vào mảng data để PDO execute dữ liệu
    } else {
        $sql = "UPDATE $table SET $update";
    }

    $tmp = $conn->prepare($sql);
    $rel = $tmp->execute($data);
    return $rel;
}

//Hàm xóa dữ liệu
function delete($table, $condition = '')
{
    global $conn;
    if (!empty($condition)) {
        $sql = "DELETE FROM $table WHERE $condition";
    } else {
        $sql = "DELETE FROM $table";
    }

    $stm = $conn->prepare($sql);
    $rel = $stm->execute();
    return $rel;
}

//Hàm lấy dữ liệu mới nhất
function lastID()
{
    global $conn;
    return $conn->lastInsertId();
}
