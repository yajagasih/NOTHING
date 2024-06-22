<?php
// Konfigurasi database
$db['default'] = array(
    'dsn'       => '',
    'hostname'  => '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 1.1.1.248)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = dbora.arindo.net)))',
    'username'  => 'web_nsra',
    'password'  => 'nsra_webbase',
    'database'  => '',
    'dbdriver'  => 'oci8',
    'dbprefix'  => '',
    'pconnect'  => FALSE,
    'cache_on'  => FALSE,
    'cachedir'  => '',
    'char_set'  => 'utf8',
    'dbcollat'  => 'utf8_general_ci',
    'swap_pre'  => '',
    'encrypt'   => FALSE,
    'compress'  => FALSE,
    'stricton'  => FALSE,
    'failover'  => array(),
    'save_queries' => TRUE
);

// Membuat koneksi ke database
$connection = oci_connect($db['default']['username'], $db['default']['password'], $db['default']['hostname']);

if (!$connection) {
    $error = oci_error();
    echo "Koneksi ke database gagal: " . $error['message'];
} else {
    // Menyiapkan dan menjalankan query update
    $update_query = "
        UPDATE sopp_nsra.mstpayment
        SET PAYMENTSAL = 6500000
        WHERE PAYMENTNAME = 'MAMASGTG' AND PAYMENTCODE = 'NSR4058'
    ";
    $stid_update = oci_parse($connection, $update_query);

    if (oci_execute($stid_update)) {
        echo "Data berhasil diperbarui.\n";
    } else {
        $error = oci_error($stid_update);
        echo "Error menjalankan query update: " . $error['message'];
    }

    // Menutup statement
    oci_free_statement($stid_update);

    // Menyiapkan dan menjalankan query untuk mendapatkan data member
    $query_member = "
        SELECT PAYMENTCODE, PAYMENTNAME, PAYMENTADDR, decode(PAYMENTSTA,'9','TDK AKTIF','AKTIF') AS PAYMENTSTA, 
        b.nama_KORWIL, PAYMENTTELP, PAYMENTSAL, a.TGL_DAFTAR 
        FROM sopp_nsra.mstpayment A, sopp_nsra.master_korwil B  
        WHERE a.kode_korwil = b.kode_korwil(+) 
        ORDER BY a.NOURUT DESC
    ";
    $stid_member = oci_parse($connection, $query_member);

    echo "<h2>Data Member</h2>";

    if (oci_execute($stid_member)) {
        $ncols = oci_num_fields($stid_member);

        echo "<table border='1'>\n";
        echo "<tr>\n";
        for ($i = 1; $i <= $ncols; $i++) {
            $colname = oci_field_name($stid_member, $i);
            echo "<th>" . htmlentities($colname, ENT_QUOTES) . "</th>\n";
        }
        echo "</tr>\n";

        while ($row = oci_fetch_array($stid_member, OCI_ASSOC+OCI_RETURN_NULLS)) {
            echo "<tr>\n";
            foreach ($row as $item) {
                echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
            }
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        $error = oci_error($stid_member);
        echo "Error menjalankan query member: " . $error['message'];
    }

    // Menutup statement
    oci_free_statement($stid_member);
}

// Menutup koneksi ketika selesai
oci_close($connection);
?>
