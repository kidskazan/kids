<? require_once "r.php"; ?>
<html>
	<head>
		<title>Админка</title>
		<style>
			td{border: 1px solid black;}
		</style>
	</head>
	<body>
		<center>
			<h2>Админка</h2>
		</center>
		<?
			$stat = new dataTable("stations");
			$stations = $stat->getFields(array("id", "name", "hash"));
			
			$mentor = new dataTable("mentor");
			$r_mentor = $mentor->getFields(array("*"));
		?>
		<div style="float: left;">
			<h3>Станции</h3>
			<table>
				<tr>
					<td><b>id</b></td>
					<td><b>Название</b></td>
					<td><b>Статус</b></td>
					<td><b>Действие</b></td>
				</tr>
				<?foreach ($stations as $val):?>
					<tr>
						<td><?echo $val["id"]?></td>
						<td><?echo $val["name"]?></td>
						<td><?if ($val["hash"] != "") echo "Авторизован"; else echo "Неавторизован";?></td>
						<td>
							<?if ($val["hash"] != ""):?>
								<a href="http://91.239.26.122/test.php?cmd=exitStation&hash=<?echo $val["hash"];?>">Выйти</a>
							<?else:?>
								<a href="http://91.239.26.122/test.php?cmd=selectStation&id=<?echo $val["id"];?>">Авторизовать</a>
							<?endif;?>
						</td>
					</tr>
				<?endforeach;?>
			</table>
		</div>
		<div style="float:left; width: 100%;">
			<h3>Наставники</h3>
			<table>
				<tr>
					<td><b>id</b></td>
					<td><b>ФИО</b></td>
					<td><b>QR</b></td>
					<td><b>Статус</b></td>
					<td><b>Действие</b></td>
				</tr>
				<?foreach ($r_mentor as $val):?>
					<tr>
						<td><?echo $val["id"];?></td>
						<td><?echo $val["name"]." ".$val["surname"]." ".$val["father_name"];?></td>
						<td><?echo $val["qr"];?></td>
						<td><?if ($val["token"] != "")echo "Авторизован"; else echo "Неавторизован";?></td>
						<td>
							<?if ($val["token"] != ""):?>
								<a href="http://91.239.26.122/test.php?cmd=logout&token=<?echo $val["token"];?>">Выйти</a>
							<?else:?>
								<form action="http://91.239.26.122/test.php">
									<input type="hidden" name="cmd" value="stationAuth">
									<input type="text" name="login" placeholder="Login">
									<input type="password" name="password" placeholder="Password">
									<select name="hash">
										<?foreach ($stations as $v):?>
											<?if ($v["hash"] != ""):?>
												<option value="<?=$v["hash"]?>"><?=$v["name"]?></option>
											<?endif;?>
										<?endforeach;?>
									</select>
									<input type="submit" value="Авторизовать">
								</form>
							<?endif;?>
						</td>
					</tr>
				<?endforeach;?>
			</table>
		</div>
	</body>
</html>