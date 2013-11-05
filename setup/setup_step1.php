<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=MacRoman">
<title>Application Setup Step 1 (App DB)</title>

<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.1/js/bootstrap.min.js"></script>
<?php 
    // require_once 'load_sql.php';

    // Kicking it old school :)
    // No Framework
    
    // Get Pre Configured Values
    $config_file = parse_ini_file("../../application/configs/local.ini.default", true, INI_SCANNER_RAW);
    $local_ini = "../../application/configs/local.ini"; 

    // var_dump($config);
    // var_dump($_POST);
    // var_dump($config2);
    // exit();

    $values = array();

    foreach ($config_file as $env => $configs) {
        foreach ($configs as $config => $value) {
            $values[$env][$config] = (isset($_POST[$env[$config]]) ? trim($_POST[$env[$config]]) : $value);
        }
    }
    
    // If Postback
    if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
        $ini_configs = array();

        foreach ($_POST as $env => $configs) {
            $ini_configs[str_replace("_:_", " : ", $env)][] = $configs;
        }
        $ini_configs = array();
        foreach ($_POST as $env => $configs) {
            foreach ($configs as $config => $value) {
                $ini_configs[str_replace("_:_", " : ", $env)][$config] = trim($value);
            }
        }

        if($_POST['create_db']) {
            $mysqli = mysqli_connect($ini_configs['production']['resources.db.params.host'], 
                        $_POST['root_user'], 
                        $_POST['root_pass'], 
                        "mysql");
                
            $res = mysqli_query($mysqli, "CREATE DATABASE IF NOT EXISTS ".$ini_configs['development : production']['resources.db.params.dbname']);

            if(!$res) {
                exit("There was an error creating the database. Go back and check your configs.");
            }
        }

        write_php_ini($ini_configs, $local_ini);
    } else {
        foreach ($config_file as $env => $configs) {
            foreach ($configs as $config => $value) {
                $values[$env][$config] = (isset($_POST[$env[$config]]) ? trim($_POST[$env[$config]]) : $value);
            }
        }
    }


    function write_php_ini($array, $file)
    {
        $res = array();
        foreach($array as $key => $val)
        {
            if(is_array($val))
            {
                $res[] = "[$key]";
                foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
            }
            else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
        }
        safefilerewrite($file, implode("\r\n", $res));
    }
    function safefilerewrite($fileName, $dataToSave)
    {    if ($fp = fopen($fileName, 'w'))
        {
            $startTime = microtime();
            do
            {            $canWrite = flock($fp, LOCK_EX);
               // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
               if(!$canWrite) usleep(round(rand(0, 100)*1000));
            } while ((!$canWrite)and((microtime()-$startTime) < 1000));

            //file was locked so now we can store information
            if ($canWrite)
            {            fwrite($fp, $dataToSave);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        }

    }
?>
</head>
<body>
    <div class="container">

        <form action="" method='POST' role='form' class="form-horizontal">
            <?php foreach ($config_file as $env => $configs): ?>
            <div class="panel panel-default">
                <div class="panel-heading"><?php echo ucwords($env); ?></div>
                <div class="panel-body">
                <?php foreach ($configs as $config => $value): ?>
                    <div class="form-group">
                        <label for="<?php echo $env . '[' . $config . ']'; ?>"class="col-sm-4 control-label"><?php echo $config; ?></label>
                        <div class="col-sm-8">
                            <input type="text" name="<?php echo $env . '[' . $config . ']'; ?>" value="<?php echo $values[$env][$config]; ?>" class="form-control">
                        </div>
                    </div>
                <?php endforeach ?>
                </div>
            </div>
            <?php endforeach ?>
            <div class="panel panel-default">
                <div class="panel-heading">Development Database</div>
                <div class="panel-body">
                    <div class="checkbox col-sm-offset-4">
                        <label for="create_db">
                            <input name="create_db" type="checkbox"> Create the development database?
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="root_user"class="col-sm-4 control-label">Root Username</label>
                        <div class="col-sm-8">
                            <input type="text" name="root_user" value="<?php echo (isset($_POST['root_user']) ? $_POST['root_user'] : ''); ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="root_pass"class="col-sm-4 control-label">Root Password</label>
                        <div class="col-sm-8">
                            <input type="text" name="root_pass" value="<?php echo (isset($_POST['root_pass']) ? $_POST['root_pass'] : ''); ?>" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-offset-4 col-sm-8">
                <button type="submit" class="btn btn-default">Submit</button>
            </div>
        </form> 
    </div>
</body>
</html>