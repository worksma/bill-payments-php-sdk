<?PHP
	if(isset($_POST['gopay'])) {
		require('library/class.onlinebank.php');
		
		define('BANK_CONF', [
			'kassaid' => 'kassa_id',							/* Индекс кассы */
			'secret_key' => 'secret_key'						/* Секретный ключ */
		]);
		
		$OnlineBank = new OnlineBank(BANK_CONF);
		
		try {
			$Uri = $OnlineBank->CreatePayment($_POST);
			
			die('<script>location.href = \'' . $Uri . '\';</script>');
		}
		catch(Exception $e) {
			die($e->getMessage());
		}
	}
?>

<html lang="ru">
	<thead>
		<title>Пример пополнения баланса</title>
		
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
		<form action="/pay.php" method="POST">
			<input type="hidden" name="gopay" value="1">
			
			<label>Сумма пополнения</label>
			<input type="number" name="amount" placeholder="Сумма пополнения" min="1" required>
			
			<label>Валюта пополнения</label>
			<select name="currency" required>
				<option selected disabled>Выберите валюту</option>
				<option value="RUB">Рубли</option>
				<option value="KZT">Тенге</option>
			</select>
			
			<label>Комментарий к заказу</label>
			<textarea rows="3" name="comment" placeholder="Комментарий" required></textarea>
			
			<label>Атрибуты (могут быть в json, возвращаются с уведомлением об оплате)</label>
			<input type="text" name="attributes" placeholder="Атрибуты" required>
			
			<input type="submit" value="Пополнить">
		</form>
	</tbody>
</html>