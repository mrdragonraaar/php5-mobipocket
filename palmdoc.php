<?php
/**
 * palmdoc.php
 * 
 * (c)2013 mrdragonraaar.com
 */
include_once('pdb.php');
include_once('lz77.php');

/**
 * PalmDOC.
 */
class palmdoc extends pdb
{
	/* Compression */
	const COMPRESSION_NONE = 1;		/* No compression */
	const COMPRESSION_PALMDOC = 2;		/* PalmDOC compression */
	const COMPRESSION_HUFF_CDIC = 17480;	/* HUFF/CDIC compression */

	/* Encryption */
	const ENCRYPTION_NONE = 0;	/* No encryption */
	const ENCRYPTION_MOBI_OLD = 1;	/* Old Mobipocket encryption */
	const ENCRYPTION_MOBI = 2;	/* Mobipocket encryption */

	public $palmdoc_header;

	/**
         * Create new PalmDOC instance.
         */
	function __construct()
	{
		$this->_init();
	}

	/**
         * Initialise PalmDOC.
         */
	protected function _init()
	{
		parent::_init();
		$this->set_palmdoc_type();
		$this->palmdoc_header = new palmdoc_header();
	}

	/**
	 * Read PalmDOC from open file stream.
	 * @param $palmdoc_f open file stream of PalmDOC file.
	 * @return non-zero on success.
	 */
	public function read($palmdoc_f)
	{
		$this->_init();

		if (parent::read($palmdoc_f))
		{
			$pdb_record_0 = $this->pdb_records->record[0]->data;
			$offset = $this->palmdoc_header->read($pdb_record_0);
			if (($offset > 0) && $this->valid_palmdoc())
				return 1;
		}

		return 0;
	}

	/**
	 * Check type and creator of Palm Database is for PalmDOC.
	 * @return non-zero if type and creator match PalmDOC.
	 */
	public function is_palmdoc()
	{
		return $this->is_pdb_type(self::PDB_TYPE_PALMDOC, 
		   self::PDB_CREATOR_PALMDOC);
	}

	/**
	 * Set type and creator of Palm Database to PalmDOC.
	 */
	public function set_palmdoc_type()
	{
		$this->set_pdb_type(self::PDB_TYPE_PALMDOC, 
		   self::PDB_CREATOR_PALMDOC);
	}

	/**
	 * Check is PalmDOC compression is no compression.
	 * @return non-zero if compression is no compression.
	 */
	public function is_compression_none()
	{
		return $this->is_compression(self::COMPRESSION_NONE);
	}

	/**
	 * Check is PalmDOC compression is palmdoc compression.
	 * @return non-zero if compression is palmdoc compression.
	 */
	public function is_compression_palmdoc()
	{
		return $this->is_compression(self::COMPRESSION_PALMDOC);
	}

	/**
	 * Check is PalmDOC header compression is huff/cdic compression.
	 * @return non-zero if compression is huff/cdic compression.
	 */
	public function is_compression_huff_cdic()
	{
		return $this->is_compression(self::COMPRESSION_HUFF_CDIC);
	}

	/**
	 * Check is PalmDOC compression matches given compression value.
	 * @param $compression compression value.
	 * @return non-zero if compression values match.
	 */
	public function is_compression($compression)
	{
		return $this->palmdoc_header->compression == $compression;
	}

	/**
	 * Check is PalmDOC compression is valid.
	 * @return non-zero if compression is valid.
	 */
	private function valid_compression()
	{
		return $this->is_compression_none() ||
		   $this->is_compression_palmdoc() ||
		   $this->is_compression_huff_cdic();
	}

	/**
	 * Check is PalmDOC encryption type is no encryption.
	 * @return non-zero if encryption type is no encryption.
	 */
	public function is_encryption_none()
	{
		return $this->is_encryption(self::ENCRYPTION_NONE);
	}

	/**
	 * Check is PalmDOC encryption type is old MOBIPocket encryption.
	 * @return non-zero if encryption type is old MOBIPocket encryption.
	 */
	public function is_encryption_mobi_old()
	{
		return $this->is_encryption(self::ENCRYPTION_MOBI_OLD);
	}

	/**
	 * Check is PalmDOC encryption type is MOBIPocket encryption.
	 * @return non-zero if encryption type is MOBIPocket encryption.
	 */
	public function is_encryption_mobi()
	{
		return $this->is_encryption(self::ENCRYPTION_MOBI);
	}

	/**
	 * Check is PalmDOC encryption type matches given encryption 
	 *    type value.
	 * @param $encryption_type encryption type value.
	 * @return non-zero if encryption type values match.
	 */
	public function is_encryption($encryption_type)
	{
		return $this->palmdoc_header->encryption_type == 
		   $encryption_type;
	}

	/**
	 * Check is PalmDOC encryption is valid.
	 * @return non-zero if encryption is valid.
	 */
	private function valid_encryption()
	{
		return $this->is_encryption_none() ||
		   $this->is_encryption_mobi_old() ||
		   $this->is_encryption_mobi();
	}

	/**
	 * Check is PalmDOC is valid.
	 * @return non-zero if valid.
	 */
	private function valid_palmdoc()
	{
		return $this->valid_encryption() && 
		   $this->valid_compression();
	}

	/**
	 * Get the PalmDOC text.
	 * @return text data.
	 */
	public function text()
	{
		/* Encryption */
		if (!$this->is_encryption_none())
			return "";

		/* No Encryption */
		$start_index = 1;
		$end_index = $this->palmdoc_header->record_count;
		if (($start_index <= 0) || ($end_index < $start_index))
			return "";

		$text = '';
		for ($index = $start_index; $index <= $end_index; $index++)
			$text .= $this->text_record($index);

		$text_length = $this->palmdoc_header->text_length;
		if (strlen($text) > $text_length)
			$text = substr($text, 0, $text_length);

		return $text;
	}

	/**
	 * Get the data of the text record at specified index.
	 * @param index index of text record.
	 * @return text record.
	 */
	public function text_record($index)
	{
		/* Not a text record */
		if (!$this->is_text_record_index($index))
			return "";

		$len = $this->pdb_records->data_len($index);
		if ($len > 0)
		{
			$text_record = 
			   $this->pdb_records->record[$index]->data;

			if ($text_record)
			{
				/* PalmDoc (LZ77) Compression */
				if ($this->is_compression_palmdoc())
					$text_record = 
					   lz77_decode($text_record);

				/* some records decompress to more than max 
				 * record size for unknown reason. 
				 * can lead to random text.
				 */
				$max_record_size = 
				   $this->palmdoc_header->record_size;
				if (strlen($text_record) > $max_record_size)
					$text_record = substr($text_record, 
					   0, $max_record_size);

				return $text_record;
			}
		}

		return "";
	}

	/**
	 * Get the index of text record in the Palm Database records 
	 *    from offset.
	 * @param $offset offset of text record.
	 * @return text data index.
	 */
	public function text_record_offset2index($offset)
	{
		$index = $offset + 1;
		if ($this->is_text_record_index($index))
			return $index;

		return 0;
	}

	/**
	 * Check if specified index is a valid text record index.
	 * @param $index index of text record.
	 * @return non-zero if valid text record index.
	 */
	public function is_text_record_index($index)
	{
		$start_index = 1;
		$end_index = $start_index + $this->palmdoc_header->record_count;

		return (($start_index > 0) && ($end_index >= $start_index) && 
		   ($index >= $start_index) && ($index <= $end_index));
	}
}

/**
 * PalmDOC Header.
 */
class palmdoc_header
{
	const PALMDOC_HEADER_LEN = 16;		/* Length of PalmDOC header */

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
		$this->compression = palmdoc::COMPRESSION_PALMDOC;
		$this->unused = 0;
		$this->text_length = 0;
		$this->record_count = 0;
		$this->record_size = 4096;
		$this->encryption_type = palmdoc::ENCRYPTION_NONE;
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

			return self::PALMDOC_HEADER_LEN;
		}

		return -1;
	}

	/**
	 * Get packed PalmDOC header.
	 * @return packed PalmDOC header.
	 */
	public function write()
	{
		return pack("nnNnnnn",
		   $this->compression,
		   $this->unused,
		   $this->text_length,
		   $this->record_count,
		   $this->record_size,
		   $this->encryption_type,
		   $this->unknown
		);
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
