<?php
// Konfigurasi database
$db['default'] = array(
    'dsn'       => '',
    'hostname'  => '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 1.1.1.248)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = dbora.arindo.net)))',
    'username' => 'web_nsra',
    'password' => 'nsra_webbase',
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
    // Menyiapkan dan menjalankan query untuk mendapatkan semua tabel dalam skema
    $query_tables = "
        SELECT table_name 
        FROM all_tables 
        WHERE owner = 'SOPP_NSRA'
    ";
    $stid_tables = oci_parse($connection, $query_tables);

    echo "<h2>Daftar Tabel dalam Skema SOPP_NSRA</h2>";

    if (oci_execute($stid_tables)) {
        echo "<table border='1'>\n";
        echo "<tr><th>TABLE_NAME</th></tr>\n";
        while ($row = oci_fetch_array($stid_tables, OCI_ASSOC+OCI_RETURN_NULLS)) {
            echo "<tr>\n";
            foreach ($row as $item) {
                echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
            }
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        $error = oci_error($stid_tables);
        echo "Error menjalankan query tabel: " . $error['message'];
    }

    // Menutup statement
    oci_free_statement($stid_tables);

    // Menyiapkan dan menjalankan query untuk mendapatkan semua kolom dalam skema
    $query_columns = "
        SELECT table_name, column_name, data_type, data_length 
        FROM all_tab_columns 
        WHERE owner = 'SOPP_NSRA'
        ORDER BY table_name, column_id
    ";
    $stid_columns = oci_parse($connection, $query_columns);

    echo "<h2>Daftar Kolom dalam Skema SOPP_NSRA</h2>";

    if (oci_execute($stid_columns)) {
        echo "<table border='1'>\n";
        echo "<tr><th>TABLE_NAME</th><th>COLUMN_NAME</th><th>DATA_TYPE</th><th>DATA_LENGTH</th></tr>\n";
        while ($row = oci_fetch_array($stid_columns, OCI_ASSOC+OCI_RETURN_NULLS)) {
            echo "<tr>\n";
            foreach ($row as $item) {
                echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
            }
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        $error = oci_error($stid_columns);
        echo "Error menjalankan query kolom: " . $error['message'];
    }

    // Menutup statement
    oci_free_statement($stid_columns);
}

// Menutup koneksi ketika selesai
oci_close($connection);
?>
