<?php
session_start();

$page = isset($_GET['page']) ? $_GET['page'] : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($page == 1) {
        $_SESSION['rows'] = intval($_POST['rows']);
        $_SESSION['cols'] = intval($_POST['cols']);
        header('Location: soal1a.php?page=2');
        exit;
    } elseif ($page == 2) {
        $_SESSION['data'] = $_POST['data'];
        header('Location: soal1a.php?page=3');
        exit;
    }
}

// Reset session if going back to page 1
if (isset($_GET['reset'])) {
    session_destroy();
    header('Location: soal1a.php?page=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Grid</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        input[type="number"],
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        
        input[type="number"]:focus,
        input[type="text"]:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }
        
        button, input[type="submit"] {
            padding: 12px 30px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            transition: background-color 0.3s;
        }
        
        button:hover, input[type="submit"]:hover {
            background-color: #45a049;
        }
        
        .reset-btn {
            background-color: #f44336;
        }
        
        .reset-btn:hover {
            background-color: #da190b;
        }
        
        .input-grid {
            display: grid;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .grid-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .grid-item {
            display: flex;
            flex-direction: column;
        }
        
        .grid-item label {
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .grid-item input {
            width: 100%;
        }
        
        .result-grid {
            display: grid;
            gap: 15px;
            margin-top: 30px;
        }
        
        .result-row {
            display: grid;
            gap: 15px;
        }
        
        .result-cell {
            border: 2px solid #333;
            padding: 15px;
            background-color: #f9f9f9;
            text-align: center;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .info-box {
            background-color: #e3f2fd;
            padding: 15px;
            border-left: 4px solid #2196F3;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .example-text {
            color: #666;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($page == 1): ?>
            <h1>Input Grid Dimensions</h1>
            <div class="info-box">
                <p><strong>Contoh : 1</strong> untuk baris</p>
                <p><strong>Contoh : 3</strong> untuk kolom</p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="rows">Inputkan Jumlah Baris:</label>
                    <input type="number" id="rows" name="rows" min="1" max="10" required>
                </div>
                
                <div class="form-group">
                    <label for="cols">Inputkan Jumlah Kolom:</label>
                    <input type="number" id="cols" name="cols" min="1" max="10" required>
                </div>
                
                <div class="button-group">
                    <input type="submit" value="SUBMIT">
                </div>
            </form>
        
        <?php elseif ($page == 2): ?>
            <h1>Input Grid Data</h1>
            <div class="info-box">
                <p>Total inputs needed: <strong><?php echo $_SESSION['rows'] * $_SESSION['cols']; ?></strong></p>
                <p><strong><?php echo $_SESSION['rows']; ?> rows</strong> × <strong><?php echo $_SESSION['cols']; ?> columns</strong></p>
            </div>
            
            <form method="POST">
                <div class="input-grid">
                    <?php
                    for ($i = 1; $i <= $_SESSION['rows']; $i++) {
                        echo '<div class="grid-row">';
                        for ($j = 1; $j <= $_SESSION['cols']; $j++) {
                            $inputName = "data[{$i}_{$j}]";
                            echo '
                            <div class="grid-item">
                                <label for="input_'.$i.'_'.$j.'">'. $i .'.' . $j . ':</label>
                                <input type="text" id="input_'.$i.'_'.$j.'" name="'.$inputName.'" required>
                            </div>
                            ';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
                
                <div class="button-group">
                    <button type="button" class="reset-btn" onclick="window.location.href='soal1a.php?reset=1'">Reset</button>
                    <input type="submit" value="SUBMIT">
                </div>
            </form>
        
        <?php elseif ($page == 3): ?>
            <h1>Result</h1>
            
            <div class="result-grid">
                <?php
                $rows = $_SESSION['rows'];
                $cols = $_SESSION['cols'];
                $data = $_SESSION['data'];
                
                for ($i = 1; $i <= $rows; $i++) {
                    echo '<div class="result-row" style="display: grid; grid-template-columns: repeat('.$cols.', 1fr); gap: 15px;">';
                    for ($j = 1; $j <= $cols; $j++) {
                        $key = "{$i}_{$j}";
                        $label = $i . '.' . $j;
                        $value = isset($data[$key]) ? htmlspecialchars($data[$key]) : '';
                        echo '<div class="result-cell">' . $label . ' : ' . $value . '</div>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
            
            <div class="button-group">
                <button type="button" class="reset-btn" onclick="window.location.href='soal1a.php?reset=1'">Reset</button>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
