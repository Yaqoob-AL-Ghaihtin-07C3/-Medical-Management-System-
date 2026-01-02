<?php

// دالة لجلب عنوان الموقع
function siteTitle()
{
    global $con;
    // استخدام Prepared Statement لتحسين الأمان والكفاءة
    $stmt = $con->prepare("SELECT title FROM site WHERE id = ?");
    if ($stmt) {
        $id = 1; // ID ثابت لبيانات الموقع
        $stmt->bind_param("i", $id); // 'i' تعني أن المتغير عدد صحيح (integer)
        $stmt->execute();
        $result = $stmt->get_result(); // الحصول على النتائج
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['title'];
        }
        $stmt->close();
    } else {
        // تسجيل الخطأ بدلاً من إيقاف البرنامج
        error_log("Failed to prepare statement for siteTitle: " . $con->error);
    }
    return "عنوان الصيدلية الافتراضي"; // قيمة افتراضية في حالة عدم العثور أو حدوث خطأ
}

// دالة لجلب اسم الموقع
function siteName()
{
    global $con;
    // يمكننا تحسين هذا لعدم تكرار الاستعلام إذا تم استدعاء siteTitle و siteName في نفس الصفحة.
    // لكن حالياً، سنبقيها منفصلة مع تحسين الأمان.
    $stmt = $con->prepare("SELECT name FROM site WHERE id = ?");
    if ($stmt) {
        $id = 1; // ID ثابت لبيانات الموقع
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['name'];
        }
        $stmt->close();
    } else {
        error_log("Failed to prepare statement for siteName: " . $con->error);
    }
    return "اسم الصيدلية الافتراضي"; // قيمة افتراضية
}

// دالة لجلب اسم المستخدم الحالي (المسجل دخوله)
function adminName()
{
    global $con;
    // يجب التحقق من وجود $_SESSION['userId'] قبل استخدامها
    if (!isset($_SESSION['userId'])) {
        return "غير مسجل الدخول";
    }

    $userId = $_SESSION['userId']; // الحصول على ID المستخدم من الجلسة
    $stmt = $con->prepare("SELECT name FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['name'];
        }
        $stmt->close();
    } else {
        error_log("Failed to prepare statement for adminName: " . $con->error);
    }
    return "المستخدم غير معروف"; // قيمة افتراضية
}

// دالة لجلب اسم المستخدم بناءً على معرف (ID) محدد
function getAdminName($id)
{
    global $con;
    // التأكد من أن الـ ID عدد صحيح لتجنب المشاكل
    $id = (int)$id;

    $stmt = $con->prepare("SELECT name FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['name'];
        }
        $stmt->close();
    } else {
        error_log("Failed to prepare statement for getAdminName: " . $con->error);
    }
    return "غير معروف"; // قيمة افتراضية
}

// دالة لجلب جميع الفئات (التصنيفات) وعرضها كخيارات <option>
function getAllCat()
{
    global $con;
    $array = $con->query("SELECT id, name FROM categories"); // تحديد الأعمدة المطلوبة
    if ($array) { // التحقق من نجاح الاستعلام
        while ($row = $array->fetch_assoc()) {
            echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
        }
    } else {
        error_log("Error fetching categories: " . $con->error);
        // يمكنك إظهار رسالة للمستخدم هنا أو العودة بشيء آخر
    }
}

?>