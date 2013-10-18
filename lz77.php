<?php
/**
 * lz77.php
 * 
 * (c)2013 mrdragonraaar.com
 */

/**
 * Decode LZ77 compressed string.
 * @param $data LZ77 compressed string.
 * @return decoded string.
 */
function lz77_decode($data)
{
	$data_decoded = '';

	$i = 0;
	while ($i < strlen($data))
	{
		$c = ord($data[$i++]) & 0x00FF;

		/* byte pair (space + char) */
		if ($c >= 0x00C0)
		{
			$data_decoded .= ' ';
			$data_decoded .= chr($c & 0x007F);
		}
		/* length, distance pair */
		else if ($c >= 0x0080)
		{
			if ($i >= strlen($data))
				continue;
			$c = ($c << 8) | (ord($data[$i++]) & 0x00FF);
			$len = 3 + ($c & 0x0007);
			$dist = ($c >> 3) & 0x07FF;
			$pos = strlen($data_decoded) - $dist;
			$j = 0;
			while ($len-- > 0)
				if ($pos < strlen($data_decoded))
					$data_decoded .= $data_decoded[$pos++];
		}
		/* literal */
		else if ($c >= 0x0009)
			$data_decoded .= chr($c);
		/* c literals */
		else if ($c >= 0x0001)
		{
			while ($c-- > 0)
				if ($i < strlen($data))
					$data_decoded .= $data[$i++];
		}
		/* literal */
		else
			$data_decoded .= chr($c);
	}

	return $data_decoded;
}

/**
 * Encode string to LZ77 compressed string.
 * @param $data string.
 * @return LZ77 compressed string.
 */
function lz77_encode($data)
{
	$data_encoded = '';

	$i = 0;
	while ($i < strlen($data))
	{
		$c = ord($data[$i]);

		/* length, distance pair */
		if ($i > 10 && (strlen($data) - $i) > 10)
		{
			$found = false;
			for ($chunk_len = 10; $chunk_len > 2; $chunk_len--)
			{
				$start = substr($data, 0, $i);
				$chunk = substr($data, $i, $chunk_len);
				$j = strrpos($start, $chunk);
				if ($j === false)
					$j = $i;
				$dist = $i - $j;
				if ($j < $i && $dist < 2047)
				{
					$found = true;
					$compound = (float)sprintf('%u',
					   ($dist << 3) + $chunk_len - 3);
					$data_encoded .= chr(0x80 + ($compound >> 8));
					$data_encoded .= chr($compound & 0xFF);
					$i += $chunk_len;
					break;
				}
			}
			if ($found)
				continue;
		}

		$i++;
		/* byte pair (space + char) */
		if ($c == 32 && $i < strlen($data))
		{
			$n = ord($data[$i]);
			if ($n >= 0x40 && $n <= 0x7F)
			{
				$data_encoded .= chr($n ^ 0x80);
				$i++;
				continue;
			}
		}

		/* literal */
		if ($c == 0 || ($c > 8 && $c < 0x80))
			$data_encoded .= chr($c);
		/* c literals */
		else
		{
			$j = $i;
			$data_temp = chr($c);
			while ($j < strlen($data) && strlen($data_temp) < 8)
			{
				$c = ord($data[$j]);
				if ($c == 0 || ($c > 8 && $c < 0x80))
					break;
				$data_temp .= chr($c);
				$j++;
			}
			$i += strlen($data_temp) - 1;
			$data_encoded .= chr(strlen($data_temp));
			$data_encoded .= $data_temp;
		}
	}

	return $data_encoded;
}

?>
