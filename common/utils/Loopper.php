<?php
namespace common\utils;

class Loopper {

	public static function printArray( array $array ) : void {

		foreach ($array as $key => $value) {
			if(is_array($value)) {
				self::printArray( $value );
			} else {
				echo "<span><b>" . $key . "</b>: " . $value . "</span><br />";
			}
		}

	}

}