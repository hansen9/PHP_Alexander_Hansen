<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character encoding
$conn->set_charset("utf8");

// Get filter parameter
$filter_hobi = isset($_GET['hobi']) ? $_GET['hobi'] : '';

// Build the SQL query
$sql = "SELECT hobi,COUNT(*) as count 
FROM hobi ";

// Add filter if provided
if (!empty($filter_hobi)) {
    $filter_hobi_escaped = $conn->real_escape_string($filter_hobi);
    $sql .= " WHERE hobi LIKE '%$filter_hobi_escaped%'";
}

// Group by hobi and order by total person (descending)
$sql .= " GROUP BY hobi ORDER BY count DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Get all hobi names for the filter dropdown
$hobi_list_sql = "SELECT DISTINCT hobi FROM hobi ORDER BY hobi";
$hobi_list_result = $conn->query($hobi_list_sql);
$hobi_options = [];
if ($hobi_list_result) {
    while ($row = $hobi_list_result->fetch_assoc()) {
        $hobi_options[] = $row['hobi'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hobi Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .filter-section {
            padding: 25px 30px;
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
            min-width: 250px;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            font-size: 0.95em;
        }

        .form-group input,
        .form-group select {
            padding: 10px 15px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            font-size: 0.95em;
            transition: border-color 0.3s;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        button {
            padding: 10px 25px;
            border: none;
            border-radius: 6px;
            font-size: 0.95em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-filter {
            background-color: #667eea;
            color: white;
        }

        .btn-filter:hover {
            background-color: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-reset {
            background-color: #6c757d;
            color: white;
        }

        .btn-reset:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .content {
            padding: 30px;
        }

        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
            color: #0c5aa0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        thead {
            background-color: #f8f9fa;
            border-top: 2px solid #dee2e6;
            border-bottom: 2px solid #dee2e6;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 0.95em;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            color: #555;
        }

        tbody tr {
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .rank-number {
            background-color: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9em;
        }

        .hobi-name {
            font-weight: 600;
            color: #333;
        }

        .person-count {
            background-color: #e7f3ff;
            color: #0c5aa0;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            min-width: 50px;
            text-align: center;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .no-data p {
            font-size: 1.1em;
            margin-bottom: 10px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-card .label {
            font-size: 0.9em;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 2em;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
            }

            .form-group {
                min-width: 100%;
            }

            .button-group {
                width: 100%;
            }

            button {
                flex: 1;
            }

            th, td {
                padding: 10px;
                font-size: 0.85em;
            }

            .header h1 {
                font-size: 1.8em;
            }

            table {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Hobi Report</h1>
            <p>Laporan Hobi Berdasarkan Jumlah Orang</p>
        </div>

        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label for="hobi_filter">Cari Hobi:</label>
                    <input 
                        type="text" 
                        id="hobi_filter" 
                        name="hobi" 
                        placeholder="Masukkan nama hobi..." 
                        value="<?php echo htmlspecialchars($filter_hobi); ?>"
                    >
                </div>
                <div class="button-group">
                    <button type="submit" class="btn-filter">🔍 Filter</button>
                    <button type="button" class="btn-reset" onclick="window.location.href='soal2.php';">🔄 Reset</button>
                </div>
            </form>
        </div>

        <div class="content">
            <?php
            // Display stats
            $total_rows = $result->num_rows;
            
            // Count total people
            $total_people_sql = "SELECT COUNT(p.id) as total FROM person p WHERE 1=1";
            if (!empty($filter_hobi)) {
                $filter_hobi_escaped = $conn->real_escape_string($filter_hobi);
                $total_people_sql .= " AND p.id IN (SELECT id FROM hobi WHERE hobi LIKE '%$filter_hobi_escaped%')";
            }
            $total_people_result = $conn->query($total_people_sql);
            $total_people = $total_people_result->fetch_assoc()['total'];
            
            $filter_text = !empty($filter_hobi) ? " (Filter: " . htmlspecialchars($filter_hobi) . ")" : "";
            ?>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="label">Total Hobi</div>
                    <div class="value"><?php echo $total_rows; ?></div>
                </div>
                <div class="stat-card">
                    <div class="label">Total Orang</div>
                    <div class="value"><?php echo $total_people; ?></div>
                </div>
            </div>

            <?php if (!empty($filter_hobi)): ?>
            <div class="info-box">
                ℹ️ Menampilkan hasil untuk: <strong><?php echo htmlspecialchars($filter_hobi); ?></strong>
            </div>
            <?php endif; ?>

            <?php if ($total_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">Ranking</th>
                        <th>Nama Hobi</th>
                        <th style="width: 150px;">Total Orang</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><div class='rank-number'>" . $rank . "</div></td>";
                        echo "<td><span class='hobi-name'>" . htmlspecialchars($row['hobi']) . "</span></td>";
                        echo "<td><span class='person-count'>" . intval($row['count']) . " orang</span></td>";
                        echo "</tr>";
                        $rank++;
                    }
                    ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">
                <p>📭 Tidak ada data hobi yang ditemukan</p>
                <?php if (!empty($filter_hobi)): ?>
                <p>Coba cari dengan kata kunci yang berbeda atau <a href="soal2.php" style="color: #667eea; text-decoration: none;">reset filter</a></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>