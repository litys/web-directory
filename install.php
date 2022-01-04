<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Instalator LITYSkatalog</title>
	<style>
	body {
		background-color: #eee;
	}

	* {
		text-align: center;
	}

	h2,
	h4 {
		margin-bottom: 0;
	}

	input {
		min-width: 250px;
	}

	input[type=submit] {
		padding: 8px;
		margin-top: 15px;
	}
	</style>
</head>

<body>

	<?php
// Wyświetlanie formularza instalacyjnego
if(!isset($_POST['admin_mail'])) :
?>

	<form action="" method="POST">
		<h1>Instalator LITYSkatalog</h1>
		<h2>Ustawienia katalogu (panel logowania do katalogu)</h2>

		<h4>Nazwa użytkownika</h4>
		<input required="required" minlength="2" type="text" name="website_login" placeholder="Login">
		<h4>Hasło użytkownika</h4>
		<input required="required" minlength="2" type="password" name="website_pass" placeholder="Hasło">

		<h2>Ustawienia maila</h2>

		<h4>Mail administratora</h4>
		<input required="required" minlength="2" type="text" name="admin_mail" placeholder="admin@mail.com">

		<h2>Ustawienia bazy</h2>

		<h4>Host</h4>
		<input required="required" minlength="2" type="text" name="admin_host" placeholder="localhost">
		<h4>Nazwa bazy danych</h4>
		<input required="required" minlength="2" type="text" name="admin_database" placeholder="nazwa bazy">
		<h4>Użytkownik</h4>
		<input required="required" minlength="2" type="text" name="admin_user" placeholder="root">
		<h4>Hasło</h4>
		<input type="password" name="admin_pass" placeholder="hasło"><br>
		<input required="required" type="submit" value="Rozpocznij instalowanie!">
	</form>

	<?php 
else :
    echo '<div>';
    // Rozpoczęcie pracy instalatora

    // Importowanie bazy i ustawianie jej
    $mysql_host = $_POST['admin_host'];
    $mysql_database = $_POST['admin_database'];
    $mysql_user = $_POST['admin_user'];
    $mysql_password = $_POST['admin_pass'];

    try {
        $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<div>Poprawnie połączono z bazą </div>";
    } catch(PDOException $e) {
        echo "Błąd połączenia: " . $e->getMessage();
        exit;
    }

    $db = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);

    $query = file_get_contents("lityskatalog.sql");

    $stmt = $db->prepare($query);

    if ($stmt->execute()){
        echo "<div>Baza przygotowana</div>";
    }
    else { 
        echo "<div>Nie można wykonac</div>";
        exit;
    }


    // Usuwanie pliku instalacyjnego
    unlink('install.php');
    // Usuwanie importu bazy
    unlink('lityskatalog.sql');

    // Tworzenie pliku konfiguracyjnego
    $config = "<?php
    \$website_login = '".$_POST['website_login']."';
    \$website_pass = '".$_POST['website_pass']."';
    \$admin_mail = '".$_POST['admin_mail']."';
    \$admin_host = '".$_POST['admin_host']."';
    \$admin_database = '".$_POST['admin_database']."';
    \$admin_user = '".$_POST['admin_user']."';
    \$admin_password = '".$_POST['admin_pass']."';";

    $conf_file = fopen("config.php", "w");
    fwrite($conf_file, $config);
    fclose($conf_file); 

    echo '<div>Poprawnie zainstalowano katalog!</div>';
    echo '<div>By zmienić dane dostępowe edytuj plik config.php</div>';
    echo '<div>Przejdź teraz do <a href="panel">panelu</a> by dostosować katalog.</div>';
    echo '</div>';

endif;
?>

</body>

</html>