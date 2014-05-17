<?php
/*
 * function value() is modified version of  
 * HttpFoundation / ParameterBag.php at
 * https://github.com/symfony/HttpFoundation/blob/master/ParameterBag.php#L103
 * https://github.com/symfony/HttpFoundation
 * 
 * 
 * Copyright (c) 2004-2014 Fabien Potencier

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
 * 
 */

function value($array, $path, $default = null)
{
	if (false === $pos = strpos($path, '[')) {
		return $array;
	}

	$value = $array;
	$currentKey = null;

	for ($i = $pos, $c = strlen($path); $i < $c; $i++) {
		$char = $path[$i];

		if ('[' === $char) {
			if (null !== $currentKey) {
				throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "[" at position %d.', $i));
			}

			$currentKey = '';
		} elseif (']' === $char) {
			if (null === $currentKey) {
				throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "]" at position %d.', $i));
			}

			if (!is_array($value) || !array_key_exists($currentKey, $value)) {
				return $default;
			}

			$value = $value[$currentKey];
			$currentKey = null;
		} else {
			if (null === $currentKey) {
				throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "%s" at position %d.', $char, $i));
			}

			$currentKey .= $char;
		}
	}

	if (null !== $currentKey) {
		throw new \InvalidArgumentException(sprintf('Malformed path. Path must end with "]".'));
	}

	return $value;
}

function convert_array_to_string($coordinates){
	$result = '';
	foreach($coordinates as $el){
		$result =$result.'['.$el.']';
	}
	
	return $result;
}
function get_value($array, $coordinates){
	$path = convert_array_to_string($coordinates);
	$result = value($array, $path);
	return $result;
}


?>