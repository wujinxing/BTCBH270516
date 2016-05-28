<?php
	require_once 'app/start.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>PHP Facebook SDK4</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
	<style>
		body {
			margin: 100px auto;
			width: 400px;
			text-align: center;
		}
	</style>
</head>
<body>
	<h2>PHP Facebook SDK v4</h2>

	<?php if (!isset($_SESSION['facebook'])): ?>
		<a href="<?php echo $helper->getLoginUrl($config['scopes']); ?>" class="btn btn-primary">Iniciar sesión con Facebook!</a>
	<?php else: ?>
                <img src="<?php echo $facebook_img['url']; ?>" />
		<p>Bienvenido, <?php echo $name; ?></p>
                <p>ID: <?php echo $id; ?></p>
                <p>Nombre: <?php echo $firstname; ?></p>
                <p>Apellido: <?php echo $lastname; ?></p>                
                <p>Email: <?php echo $email; ?></p>
                <p>Sexo: <?php echo $genero; ?></p>
                <p>F.Nac: <?php echo $fechanac; ?></p>
                <p>img1:  <img src="<?php echo $image; ?>" /></p>                
                <p>Link: <?php echo $link; ?></p>
		<a href="app/logout.php" class="btn btn-danger">Cerrar sesión</a>
	<?php endif; ?>
</body>
</html>