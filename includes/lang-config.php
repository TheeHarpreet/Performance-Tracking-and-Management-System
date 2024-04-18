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
            "Personal Particulars"=>"Khusus Peribadi", 
            "Professional Achievements"=>"Pencapaian Peribadi",
            "Research And Development"=>"Pembangun Penyilidik", 
            "Professional Consultations"=>"Perundingan profesional",
            "Research Outcomes"=>"Hasil Penyelidik",
            "Professional Recognition"=>"Pengiktirafan profesional", 
            "Service To Community"=>"Servis kepada Komuniti",
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
        )
    );

    $language = $_SESSION['language'];
    return isset($translations[$language][$key]) ? $translations[$language][$key] : $key;
}
?>