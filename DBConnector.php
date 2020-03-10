<?php

class DBConnector {

	protected $url = 'localhost';
	protected $user = 'root';
	protected $password = '';
	protected $db = 'medesign';
	protected $connection = null;

	public function __construct() {
		$this->make_connection();
		$this->create_new_db();
	}

	public function __destruct() {
		$this->close_connection();
	}

	protected function make_connection() {
		$this->connection = new mysqli($this->url, $this->user, $this->password, $this->db);
		if ($this->connection->connect_error)
			echo "Ошибка:" . $this->connection->connect_error;
	}

	protected function close_connection() {
		if ($this->connection != null) {
			$this->connection->close();
			$this->connection = null;
		}
	}

	protected function create_new_db() {
		$query =
			'CREATE TABLE IF NOT EXISTS `string_list` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`string` varchar(255) NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `string` (`string`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

		$this->connection->query($query);
	}

	/*
	* @comment:
	* Думал, достаточно ли будет длины для поля `string` в 255 символов
	* - можно было сделать алгоритм по-другому - без UNIQUE KEY,
	* но учитывая требования к максимальному быстродействию, решил реализовать именно таким образом.
	*/
	public function insert_array($string_array) {
		$prepared_values = '';
		#Подготовка, чтоб отправить строки одним запросом
		foreach ($string_array as $string)
			$prepared_values .= "('" . $this->connection->real_escape_string($string) . "'),";
		$prepared_values = substr($prepared_values, 0, -1);

		$this->connection->query("INSERT IGNORE INTO string_list (`string`) VALUES {$prepared_values};");
		echo 'Данные <i>успешно отправлены</i> в базу! (Но не факт, что <i>успешно дошли</i> Кхе-Хе &#128540;)';
		echo '<br>Кол-во вставленных строк: ' . $this->connection->affected_rows;
	}

}

?>
