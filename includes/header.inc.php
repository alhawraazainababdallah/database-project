<?php
require_once("database.inc.php");

require_once("auth.inc.php");

require_once("helpers.inc.php");


$pageTitle = "Renter";
$pageDescription = "";

if(isset($title)) $pageTitle = $title;
if(isset($description)) $pageDescription = $description;


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?=$pageTitle?> - Vehicle Renting</title>
	<link rel="stylesheet" href="./style/main.css">
	<link rel="stylesheet" href="./style/header.css">
</head>
<body>
	<header>
		<div class="titles">
			<h2>
				<?=$pageTitle?>
			</h2>
			<small>
				<?=$pageDescription?>
			</small>
		</div>
		<div class="profile">
			<div class="circle"></div>
			<p><?=$_SESSION["name"]?></p>
		</div>
	</header>
	<aside>
		<div class="logo">
			<img src="./images/icon.png" alt="Logo" />
			<span>RENTER</span>
		</div>
		<nav>
			<ul>
				<?php
				foreach(getFirstNav() as $item) {
					if(!$item["hasPermission"]) continue;

					echo "<li>
						<a href='{$item["href"]}'  class='{$item["class"]}'>
							{$item["name"]}
						</a>
					</li>";
				}
				?>
				
			</ul>
		</nav>
		<hr />
		<nav>
			<ul>
			<?php
				foreach(getSecondNav() as $item) {
					if(!$item["hasPermission"]) continue;

					echo "<li>
						<a href='{$item["href"]}'  class='{$item["class"]}'>
							{$item["name"]}
						</a>
					</li>";
				}
				?>
			</ul>
		</nav>
	</aside>
	<main>

