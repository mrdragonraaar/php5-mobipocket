<?php
/**
 * palmdoc_header.php
 * 
 * (c)2013 mrdragonraaar.com
 */

/**
 * PalmDOC Header.
 */
class palmdoc_header
{
	const PALMDOC_HEADER_LEN = 16;		/* Length of PalmDOC header */

	/* Compression */
	const COMPRESSION_NONE = 0;		/* No compression */
	const COMPRESSION_PALMDOC = 2;		/* PalmDOC compression */
	const COMPRESSION_HUFF_CDIC = 17480;	/* HUFF/CDIC compression */

	/* Encryption */
	const ENCRYPTION_NONE = 0;	/* No encryption */
	const ENCRYPTION_MOBI_OLD = 1;	/* Old Mobipocket encryption */
	const ENCRYPTION_MOBI = 2;	/* Mobipocket encryption */

	public $compression;
	public $unused;
	public $text_length;
	public $record_count;
	public $record_size;
	public $encryption_type;
	public $unknown;

	/**
         * Create new PalmDOC header instance.
         */
	function __construct()
	{
		$this->_init();
	}

	/**
         * Initialise PalmDOC header.
         */
	private function _init()
	{
		$this->compression = 0;
		$this->unused = 0;
		$this->text_length = 0;
		$this->record_count = 0;
		$this->record_size = 0;
		$this->encryption_type = 0;
		$this->unknown = 0;
	}

	/**
	 * Get PalmDOC header from PDB record 0.
	 * @param $pdb_record_0 PDB record 0.
	 * @return offset in PDB record 0 (should be 16 if successful).
	 */
	public function read($pdb_record_0)
	{
		if ($palmdoc_header_buf = substr($pdb_record_0, 0, 
		   self::PALMDOC_HEADER_LEN))
		{
			$palmdoc_header = unpack("ncompression/nunused/Ntext_length/nrecord_count/nrecord_size/nencryption_type/nunknown", $palmdoc_header_buf);

			/* Compression */
			if (isset($palmdoc_header['compression']))
				$this->compression = 
				   $palmdoc_header['compression'];
			/* Unused */
			if (isset($palmdoc_header['unused']))
				$this->unused = $palmdoc_header['unused'];
			/* Text Length */
			if (isset($palmdoc_header['text_length']))
				$this->text_length = 
				   $palmdoc_header['text_length'];
			/* Record Count */
			if (isset($palmdoc_header['record_count']))
				$this->record_count = 
				   $palmdoc_header['record_count'];
			/* Record Size */
			if (isset($palmdoc_header['record_size']))
				$this->record_size = 
				   $palmdoc_header['record_size'];
			/* Encryption Type */
			if (isset($palmdoc_header['encryption_type']))
				$this->encryption_type = 
				   $palmdoc_header['encryption_type'];
			/* Unknown */
			if (isset($palmdoc_header['unknown']))
				$this->unknown = 
				   $palmdoc_header['unknown'];

			if ($this->valid())
				return self::PALMDOC_HEADER_LEN;
		}

		return -1;
	}

	/**
	 * Check is PalmDOC header compression is no compression.
	 * @return non-zero if compression values match.
	 */
	public function is_compression_none()
	{
		return $this->is_compression(self::COMPRESSION_NONE);
	}

	/**
	 * Check is PalmDOC header compression is palmdoc compression.
	 * @return non-zero if compression values match.
	 */
	public function is_compression_palmdoc()
	{
		return $this->is_compression(self::COMPRESSION_PALMDOC);
	}

	/**
	 * Check is PalmDOC header compression is huff/cdic compression.
	 * @return non-zero if compression values match.
	 */
	public function is_compression_huff_cdic()
	{
		return $this->is_compression(self::COMPRESSION_HUFF_CDIC);
	}

	/**
	 * Check is PalmDOC header compression matches given compression value.
	 * @param $compression compression value.
	 * @return non-zero if compression values match.
	 */
	public function is_compression($compression)
	{
		return $this->compression == $compression;
	}

	/**
	 * Check is PalmDOC header compression is valid.
	 * @return non-zero if compression is valid.
	 */
	private function valid_compression()
	{
		return $this->is_compression_none() ||
		   $this->is_compression_palmdoc() ||
		   $this->is_compression_huff_cdic();
	}

	/**
	 * Check is PalmDOC header encryption type is no encryption.
	 * @return non-zero if encryption type values match.
	 */
	public function is_encryption_none()
	{
		return $this->is_encryption(self::ENCRYPTION_NONE);
	}

	/**
	 * Check is PalmDOC header encryption type is old MOBIPocket encryption.
	 * @return non-zero if encryption type values match.
	 */
	public function is_encryption_mobi_old()
	{
		return $this->is_encryption(self::ENCRYPTION_MOBI_OLD);
	}

	/**
	 * Check is PalmDOC header encryption type is MOBIPocket encryption.
	 * @return non-zero if encryption type values match.
	 */
	public function is_encryption_mobi()
	{
		return $this->is_encryption(self::ENCRYPTION_MOBI);
	}

	/**
	 * Check is PalmDOC header encryption type matches given encryption 
	 * type value.
	 * @param $encryption_type encryption type value.
	 * @return non-zero if encryption type values match.
	 */
	public function is_encryption($encryption_type)
	{
		return $this->encryption_type == $encryption_type;
	}

	/**
	 * Check is PalmDOC header encryption is valid.
	 * @return non-zero if encryption is valid.
	 */
	private function valid_encryption()
	{
		return $this->is_encryption_none() ||
		   $this->is_encryption_mobi_old() ||
		   $this->is_encryption_mobi();
	}

	/**
	 * Check is PalmDOC header is valid.
	 * @return non-zero if valid.
	 */
	private function valid()
	{
		return ($this->record_count &&
		   $this->valid_encryption() && 
		   $this->valid_compression());
	}

	/**
	 * Print PalmDOC header.
	 */
	public function display()
	{
		echo "Compression: $this->compression\n";
		echo "Unused: $this->unused\n";
		echo "Text Length: $this->text_length\n";
		echo "Record Count: $this->record_count\n";
		echo "Record Size: $this->record_size\n";
		echo "Encryption: $this->encryption_type\n";
		echo "Unknown: $this->unknown\n";
	}
}

?>
