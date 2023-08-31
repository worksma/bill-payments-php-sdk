<?PHP
	if(isset($_POST['hash'])) {
		require(__DIR__ . '/../src/class.onlinebank.php');
		
		define('BANK_CONF', [
			'kassaid' => 'kassa_id',							/* Индекс кассы */
			'secret_key' => 'secret_key'						/* Секретный ключ */
		]);
		
		$OnlineBank = new OnlineBank(BANK_CONF);
		
		if($OnlineBank->IsPaymentSuccess($_POST['hash'])) {
			die('Счёт оплачен');
		}
		else {
			die('Счёт не оплачен');
		}
	}
?>

<html lang="ru">
	<thead>
		<title>Проверка оплаты</title>
		
		<style>
			form {
				display: flex;
				flex-direction: column;
				gap: 14px;
				max-width: 40%;
			}
		</style>
	</thead>
	
	<tbody>
		<form action="/checking.php" method="POST">
			<input type="text" name="hash" placeholder="Хэш для проверки" required>
			
			<input type="submit" value="Проверить">
		</form>
	</tbody>
</html>