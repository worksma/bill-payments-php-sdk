<?PHP
	class OnlineBank {
		/*
			Выставляем конфигурации
		*/
		var $_Conf;
		
		public function __construct(array $_ArrayData) {
			$this->_Conf = $_ArrayData;
		}
		
		/*
			Получаем сервер Платёжного агрегата
		*/
		public function Server($_Method = '') {
			return 'https://oplata.awscode.ru' . $_Method;
		}
		
		/*
			Создаём платёжный запрос
			
			- Данные для создания запроса ArrayData:
			* amount - сумма платежа
			* currency - валюту пополнения (KZT, RUB) от 31.08.2023
			* comment - комментарий продавца
			* attributes - атрибуты
			
			- В случае успеха, вернёт:
			* alert[string * success] - ответ
			* uri[string] - ссылка на оплату
			
			- В случае неудачи, вернёт:
			* alert[string * warning/error] - ответ
			* message[string] - причина неудачи
		*/
		public function CreatePayment(array $_ArrayData) {
			$_Result = $this->Put($this->Server('/creation'), ['price' => $_ArrayData['amount'] * 1.00, 'currency' => $_ArrayData['currency'], 'shop' => $this->_Conf['kassaid'], 'secret' => $this->_Conf['secret_key'], 'comment' => $_ArrayData['comment'], 'attributes' => $_ArrayData['attributes']]);
			
			if($_Result = json_decode($_Result)) {
				switch($_Result->alert) {
					case 'success': {
						return $_Result->uri;
						break;
					}
					default: {
						throw new Exception($_Result->message);
					}
				}
			}
		}
		
		/*
			Проверяем данные, полученные после оплаты
			
			- В случае успеха, вернёт данные:
			* price[int] - сумма пополнения;
			* attributes[string] - ранее отправленные атрибуты;
			* hash[string] - уникальный платёжный ключ (по которому создавался платёж).
			
			- В случае неудачи, вернёт: NULL
			$_IsValid = IsPaymentValid(json_decode(file_get_contents('php://input'), true));
			
			if(empty($_IsValid)) {
				http_response_code(204);
				exit('Error: [empty data]');
			}
		*/
		public function IsPaymentValid($_Data) {
			if($this->IsPaymentSuccess($_Data['hash'])) {
				unset($_Data['hash']);
				return $_Data;
			}
			
			return null;
		}
		
		/*
			Проверяем, оплачен ли счёт или нет
			
			- В случае успеха, вернёт: yes;
			- В случае ожидания, вернёт: no.
		*/
		public function IsPaymentSuccess($_Hash) {
			$_Result = $this->Put($this->Server('/checking/' . $_Hash), []);
			
			if($_Result = json_decode($_Result)) {
				if($_Result[0] == 'yes') {
					return true;
				}
			}
			
			return false;
		}
		
		/*
			Отправка POST запросов
		*/
		public function Put($_Site, $_PostFields) {
			$_Init = curl_init($_Site);
			curl_setopt($_Init, CURLOPT_POST, 1);
			curl_setopt($_Init, CURLOPT_POSTFIELDS, $_PostFields);
			curl_setopt($_Init, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($_Init, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($_Init, CURLOPT_HEADER, false);
			$_Result = curl_exec($_Init);
			curl_close($_Init);
			
			return $_Result;
		}
	}