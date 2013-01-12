<?php
/**
 * base_header.php
 * 
 * (c)2013 mrdragonraaar.com
 */

/**
 * Base Header.
 */
class base_header
{
	const BASE_HEADER_LEN = 8;	/* Length of a header entries' header.
					   Size of identifier and header length
					   fields. */

	public $identifier;
	public $header_length;
	public $data;

	/**
         * Create new base header instance.
         */
	function __construct()
	{
		$this->_init();
	}

	/**
         * Initialise base header.
         */
	protected function _init()
	{
		$this->identifier = "";
		$this->header_length = 0;
		$this->data = "";
	}

	/**
	 * Get base header from PDB record 0.
	 * @param $pdb_record_0 PDB record 0.
	 * @param $offset offset in PDB record 0.
	 * @return offset in PDB record 0.
	 */
	public function read($pdb_record_0, $offset)
	{
		if ($base_header_buf = substr($pdb_record_0, $offset, 
		   self::BASE_HEADER_LEN))
		{
			$base_header = unpack("a4identifier/Nheader_length",
			   $base_header_buf);

			/* Identifier */
			if (isset($base_header['identifier']))
				$this->identifier = $base_header['identifier'];
			/* Header Length */
			if (isset($base_header['header_length']))
				$this->header_length = 
				   $base_header['header_length'];

			$offset += self::BASE_HEADER_LEN;

			if ($this->header_length > self::BASE_HEADER_LEN)
			{
				$data_length = $this->header_length - 
				   self::BASE_HEADER_LEN;

				/* Data */
				if ($data_buf = substr($pdb_record_0, $offset, 
				   $data_length))
				{
					$this->data = $data_buf;
					$offset += $data_length;

					return $offset;
				}
			}
		}

		return -1;
	}

	/**
	 * Check if header is of header type.
	 * @param $header_type header type string.
	 * @return non-zero if header is of header type.
	 */
	public function is_header_type($header_type)
	{
		return $this->identifier == $header_type;
	}

	/**
	 * Print base header.
	 */
	public function display()
	{
		echo "Identifier: $this->identifier\n";
		echo "Header Length: $this->header_length\n";
	}
}

?>
