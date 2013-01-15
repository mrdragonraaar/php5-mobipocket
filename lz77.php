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

?>
