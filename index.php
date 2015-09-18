<?php 

	// @author Ahmet Sarıdoğan
	// Veri tabanı işlemlerimizi PDO ile yapacağız.
	
	$DIR = "http://localhost/short.url/";

	try {
		$db   = "short_url";
		$user = "root";
		$pass = "";
		$dsn  = "mysql:host=localhost;dbname=".$db.";charset=utf8";
		$db   = new PDO($dsn,$user,$pass);
		$db->query("SET CHARACTER SET utf8");
	} catch (PDOException $e) {
		echo "Hata : ".$e->getMessage()."<br/>";
		die();
	}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Url kısaltma Scripti</title>
	<meta author="Ahmet Sarıdoğan" content="http://ahmetsaridogan.com/">
	<style>
		html,body{
			font-family: "Open Sans";
			background-color: #F1F1F1;
		}
		.wrapper{
			width: 500px;
			padding: 10px;
			height: 300px;
			margin: 0 auto;
			background-color: #fff;
			box-shadow: 0 2px 5px 0 rgba(0,0,0,.26);
		}
	</style>
</head>
<body>	
	
	<?php 

		if (isset($_GET["url"])){
			// Burda url mizi kontrol ediyoruz parametre varsa yonlendirecegiz
			$short_url =  $DIR.$_GET["url"];
			// parametreye bakıp yonlendiriyoruz
			$query = $db->prepare("SELECT * FROM shortener WHERE short_url = :short_url");
			$query->execute(array('short_url' => $short_url));
			$result = $query->fetch(PDO::FETCH_ASSOC);
			if ($query->rowCount()) {
				header("Location: ".$result["url"]);
				echo $result["url"];
			}
		}

	?>


	<?php 

		// Burada daha önce kısaltılmıssa onu verdik eger yoksa yenı kayıt yaptık url kısalttık
		if (isset($_POST["submit"])) {
			// input de url bos olup olmadıgna bakıyoruz
			if (!empty($_POST["url"])) {
				$url = trim($_POST["url"]);
				$length = 10;
				// Url yi kısalttık
				$short_url = $DIR.substr(str_shuffle($url."0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

				// daha önce url eklendmismi eklendiyse birşey yapmıyoruz
				$query = $db->prepare("SELECT * FROM shortener WHERE url = :url");
				$query->execute(array('url' => $url));
				$result = $query->fetch(PDO::FETCH_ASSOC);
				// Eklenmediyse ekliyoruz ekrana link basıyoruz
				if (!$query->rowCount()) {
					$insert = $db->prepare("INSERT INTO shortener (url,short_url) VALUES (:url,:short_url)"); 
					$new = $insert->execute(
						array(
							'url'       => $url,
							'short_url'     => $short_url,
						));
						echo $short_url = $short_url;
					}else{

					$short_url = $result["short_url"];
				}

			}
		}

	?>

	<div class="wrapper">
		
		<form method="post">
			<label for="">Url:</label><br>
			<input type="text" name="url"><br>
			<button name="submit">Kısalt</button>
		</form>
		<?php if (isset($short_url)): ?>
			<a href="<?php echo $short_url ?>" target="_blank"><?php echo $short_url ?></a>
		<?php endif ?>
	</div>
	
</body>
</html>