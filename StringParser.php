<?php

class StringParser {

	public static function to_array($raw_string) {
		$open_brackets_count = substr_count($raw_string, '{');
		$close_brackets_count = substr_count($raw_string, '}');

		if ($open_brackets_count != $close_brackets_count)
			exit("Ошибка: Неправильное соответствие открывающих({$open_brackets_count}) и закрывающих({$close_brackets_count}) скобок!");

		return self::expand_string_to_array( self::reduce_nested_brackets($raw_string) );
	}

	private static function reduce_nested_brackets($raw_string) {
		#(всё остальное перед..) ("множитель" перед..) {(содержание самых внутренних скобок)} (.."множитель" после) (..всё остальное после)
		$re = '#(.* [|{]) ([^|{}]*) {([^{}]+)} ([^|{}]*) ([|}] .*)#ixs';
		$re_matches = [];
		$options = [];
		$options_string = '';

		#Если во входных данных нет вложенных скобок
		if (!preg_match($re, $raw_string, $re_matches))
			return $raw_string;

		#если же вложенные скобки есть
		$options = explode('|', $re_matches[3]);
		for ($i=0; $i < count($options); $i++)
			$options_string .= $re_matches[2] . $options[$i] . $re_matches[4] . '|';
		#Удаление посленего символа | в строке
		$options_string = substr($options_string, 0,-1);
		#Подстановка начального и конечного значения из исходной строки
		$prepared_string = $re_matches[1] . $options_string . $re_matches[5];

		return self::reduce_nested_brackets($prepared_string);
	}

	private static function expand_string_to_array($prepared_string) {
		return self::replace_brackets_with_contents([$prepared_string]);
	}

	private static function replace_brackets_with_contents($strings_in) {
		$re_brackets = '# { [^{}]+ } #ixs';
		$brackets_match = [];
		$strings_out = [];

		if (preg_match($re_brackets, $strings_in[0], $brackets_match)) {
		#Если во входных данных есть что парсить
			for ($i=0; $i < count($strings_in); $i++) {
				$options = explode('|', str_replace(['{', '}'], '', $brackets_match[0]) );
				for ($j=0; $j < count($options); $j++)
					$strings_out[] = preg_replace($re_brackets, $options[$j], $strings_in[$i], 1);
			}

			return self::replace_brackets_with_contents($strings_out);
		} else {
			return $strings_in;
		}
	}
}

?>
