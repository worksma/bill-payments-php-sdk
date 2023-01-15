<?PHP
	require('library/class.onlinebank.php');
	
	define('BANK_CONF', [
		'kassaid' => 'kassa_id',						/* Индекс кассы */
		'secret_key' => 'secret_key'					/* Секретный ключ */
	]);
	
	$OnlineBank = new OnlineBank(BANK_CONF);
	
	/*
		Создание платежа
	*/
	if(isset($_GET['create'])) {
		var_dump($OnlineBank->CreatePayment([
			'amount' => 500,								/* Сумма пополнения */
			'comment' => 'Пополнение профиля ID: 1',		/* Комментарий продавца */
			'attributes' => '1'								/* Атрибуты (могут быть в JSON) */
		]));
	}
	
	/*
		Проверка платежа
	*/
	$_Hash = 'a9e4ca246fefd6a0e8e7b70b49bc8f60';		/* Полученный хэш от системы (уведомления) */
	
	if($OnlineBank->IsPaymentSuccess($_Hash)) {
		echo 'Оплата прошла';
	}
	else {
		echo 'Счёт не оплачен';
	}