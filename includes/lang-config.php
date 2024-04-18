<?php

if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en';
}

if (isset($_POST['lang'])) {
    $_SESSION['language'] = $_POST['lang'];
}

if ($_SESSION['language'] != "en") {
    echo "<script src='js/translate.js'></script>";
}

function translate($key) {
    $translations = array(
        /*
        "en" => array(
            "Home" => "Home",
            "Your account doesn't have a role assigned. Please speak to an admin to assign you one." => "Your account doesn't have a role assigned. Please speak to an admin to assign you one.",
            "Select a researcher to view their work" => "Select a researcher to view their work",
            "Not enough data to calculate scores" => "Not enough data to calculate scores",
            "Performance Overview" => "Performance Overview",
            "Submissions" => "Submissions",
            "Hide" => "Hide",
            "Add New Submission" => "Add New Submission",
            "New Password" => "New Password",
            "Change Password" => "Change Password",
            "Personal Particulars"=>"Personal Particulars",
            "Research And Development"=>"Research And Development",
            "Professional Consultations"=>"Professional Consultations",
            "Research Outcomes"=>"Research Outcomes",
            "Professional Recognition"=>"Professional Recognition",
            "Service To Community"=>"Service To Community"
        ),
        */
        "bm" => array(
            "Home" => "Laman Utama",
            "Your account doesn't have a role assigned. Please speak to an admin to assign you one." => "Akaun anda tidak mempunyai peranan yang diberikan. Sila bercakap dengan pentadbir untuk memberikan anda satu.",
            "Select a researcher to view their work" => "Pilih penyelidik untuk melihat kerja mereka",
            "Not enough data to calculate scores" => "Tidak cukup data untuk mengira skor",
            "Performance Overview" => "Gambaran Prestasi",
            "Submissions" => "Penyerahan",
            "Hide" => "Sembunyi",
            "Add New Submission" => "Tambah Penyerahan Baru",
            "New Password" => "Kata Laluan Baru",
            "Change Password" => "Tukar Kata Laluan",
            "Personal Particulars" => "Khusus Peribadi",
            "Professional Achievements" => "Pencapaian Peribadi",
            "Research And Development" => "Pembangun Penyilidik",
            "Professional Consultations" => "Perundingan profesional",
            "Research Outcomes" => "Hasil Penyelidik",
            "Professional Recognition" => "Pengiktirafan profesional",
            "Service To Community" => "Servis kepada Komuniti",
            "First Name" => "Nama Pertama",
            "Last Name" => "Nama Akhir",
            "Email" => "Emel",
            "Job Role" => "Peranan Pekerjaan",
            "Update" => "Kemaskini",
            "Reset Password" => "Tetapkan Semula Kata Laluan",
            "Passwords are reset to \"Password123\"" => "Kata Laluan telah ditetapkan semula kepada \"Password123\"",
            "None" => "Tiada",
            "Researcher" => "Penyelidik",
            "Supervisor" => "Penyelia",
            "Manager" => "Pengurus",
            "Create User Account" => "Cipta Akaun Pengguna",
            "This email is already in use, please try another email." => "Emel ini sudah digunakan, sila cuba emel yang lain.",
            "Password" => "Kata Laluan",
            "Create Account" => "Cipta Akaun",
            "List Of User Accounts" => "Senarai Akaun Pengguna",
            "UserID" => "ID Pengguna",
            "Sort by" => "Disusun mengikut",
            "Edit" => "Edit",  // NeedsTranslation
            "Delete" => "Padam",
            "Unblock" => "Buka Kunci",
            "Block" => "Kunci",
            "Log In" => "Log Masuk",
            "Invalid password" => "Kata Laluan tidak sah",
            "User not found" => "Pengguna tidak dijumpai",
            "Login" => "Log Masuk",
            "Don't have an account?" => "Tiada akaun?",
            "Register here" => "Daftar di sini",
            "Language" => "Bahasa",
            "English" => "Inggeris",
            "BM" => "BM",
            "All fields are required." => "Semua medan diperlukan.",
            "Email is not valid." => "Emel tidak sah.",
            "Password must be at least 8 characters long." => "Kata laluan mesti sekurang-kurangnya 8 aksara.",
            "Passwords do not match." => "Kata laluan tidak sepadan.",
            "Email is already in use" => "Emel telah digunakan",
            "You are registered successfully." => "Anda telah berdaftar dengan berjaya.",
            "Signup" => "Daftar",
            "First name" => "Nama pertama",
            "Surname" => "Nama keluarga",
            "Confirm Password" => "Sahkan Kata Laluan",
            "Have an account?" => "Sudah mempunyai akaun?",
            "View Submission" => "Lihat Penyerahan",
            "By" => "Oleh",
            "Date Submitted" => "Tarikh Penyerahan",
            "Status" => "Status",
            "Approved" => "Diluluskan",
            "Needing Manager approval" => "Memerlukan kelulusan Pengurus",
            "Rejected" => "Ditolak",
            "Needing Supervisor approval" => "Memerlukan kelulusan Penyelia",
            "Coauthors" => "Penulis Bersama",
            "Please review work" => "Sila semak kerja",
            "Approve" => "Luluskan",
            "Comments (For declines only)" => "Komen (Hanya untuk penolakan)",
            "Return" => "Kembali",
            "You can only view details of this task" => "Anda hanya boleh melihat butiran tugasan ini",
            "Resubmit" => "Serah semula",
            "Search work" => "Cari kerja",
            "Both" => "Kedua-dua",
            "Not enough data to calculate scores" => "Tidak cukup data untuk mengira skor",
            "Search" => "Cari",
            "Database error: " => "Ralat pangkalan data: ",
            "File upload failed." => "Muat naik fail gagal.",
            "Files uploaded successfully." => "Fail-fail dimuat naik dengan berjaya.",
            "Submission Form" => "Borang Penyerahan",
            "Title" => "Tajuk",
            "Comments" => "Komen",
            "Upload File" => "Muat Naik Fail",
            "Submit" => "Hantar",
            "Logout" => "Log Keluar",
            
        )
    );

    $language = $_SESSION['language'];
    return isset($translations[$language][$key]) ? $translations[$language][$key] : $key;
}
?>