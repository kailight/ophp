<?php

namespace o;

class oDatabaseResult extends oArray {


	function findBy($key=null,$value=null) {

		foreach ($this->currentArray as $k=>$v) {

			foreach ($v as $k2=>$v2) {
				if ($key && $value) {
					if ( $v2 == $value && $k2 == $key ) {
						return $this->currentArray[ $k ];
					}
				}
				else if ($key && $k2 == $key) {
					return $this->currentArray[$k];
				} else if ($value && $v2 == $value) {
					return $this->currentArray[ $k ];
				}
			}

		}

	}


}