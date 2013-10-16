<?php
/**
 * pdb.php
 * 
 * (c)2013 mrdragonraaar.com
 */

/**
 * Palm Database.
 */
class pdb
{
	/* Palm Database Types & Creators */
	const PDB_TYPE_MOBI = 'BOOK';		/* Type: MOBI */
	const PDB_CREATOR_MOBI = 'MOBI';	/* Creator: MOBI */
	const PDB_TYPE_PALMDOC = 'TEXt';	/* Type: PalmDOC */
	const PDB_CREATOR_PALMDOC = 'REAd';	/* Creator: PalmDOC */

	public $pdb_header;
	public $pdb_records;

	/**
         * Create new Palm Database instance.
         */
	function __construct()
	{
		$this->_init();
	}

	/**
         * Initialise Palm Database.
         */
	protected function _init()
	{
		$this->pdb_header = new pdb_header();
		$this->pdb_records = new pdb_records();
	}

	/**
	 * Load Palm Database file.
	 * @param $pdb_file PDB file.
	 * @return non-zero on success.
	 */
	public function load($pdb_file)
	{
		$rv = 0;

		if ($pdb_f = fopen($pdb_file, "r"))
		{
			$rv = $this->read($pdb_f);

			fclose($pdb_f);
		}

		return $rv;
	}

	/**
	 * Read Palm Database from open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	public function read($pdb_f)
	{
		if (!is_resource($pdb_f))
			return 0;

		if (!$this->pdb_header->read($pdb_f))
			return 0;

		if (!$this->pdb_records->read($pdb_f))
			return 0;

		return 1;
	}

	/**
	 * Save Palm Database file.
	 * @param $pdb_file PDB file.
	 * @return non-zero on success.
	 */
	public function save($pdb_file)
	{
		$rv = 0;

		if ($pdb_f = fopen($pdb_file, "w"))
		{
			$rv = $this->write($pdb_f);

			fclose($pdb_f);
		}

		return $rv;
	}

	/**
	 * Write Palm Database to open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	public function write($pdb_f)
	{
		if (!is_resource($pdb_f))
			return 0;

		if (!$this->pdb_header->write($pdb_f))
			return 0;

		if (!$this->pdb_records->write($pdb_f))
			return 0;

		return 1;
	}

	/**
	 * Set name of Palm Database.
	 * @param $name name.
	 */
	public function set_pdb_name($name)
	{
		$this->pdb_header->set_name($name);
	}

	/**
	 * Set creation date of Palm Database to now.
	 */
	public function set_pdb_ctime()
	{
		$this->pdb_header->set_ctime();
	}

	/**
	 * Set modification date of Palm Database to now.
	 */
	public function set_pdb_mtime()
	{
		$this->pdb_header->set_mtime();
	}

	/**
	 * Check type and creator of Palm Database.
	 * @param $type type string.
	 * @param $creator creator string.
	 * @return non-zero if PDB is of type and creator.
	 */
	public function is_pdb_type($type, $creator)
	{
		return ($this->pdb_header->type == $type) && 
		   ($this->pdb_header->creator == $creator);
	}

	/**
	 * Set type and creator of Palm Database.
	 * @param $type type string.
	 * @param $creator creator string.
	 */
	public function set_pdb_type($type, $creator)
	{
		$this->pdb_header->set_type($type, $creator);
	}

	/**
	 * Add Palm Database record.
	 * @param $data data to add.
	 * @return palm database record.
	 */
	public function add_pdb_record($data)
	{
		$pdb_record = $this->pdb_records->add_record($data);
		$this->pdb_header->unique_id_seed = $this->pdb_records->num_records;

		return $pdb_record;
	}

	/**
	 * Set Palm Database record.
	 * @param $rec_index index of record.
	 * @param $data data to set.
	 * @return palm database record.
	 */
	public function set_pdb_record($rec_index, $data)
	{
		return $this->pdb_records->set_record($rec_index, $data);
	}

	/**
	 * Remove Palm Database record.
	 * @param $rec_index index of record.
	 * @return true if removed.
	 */
	public function remove_pdb_record($rec_index)
	{
		return $this->pdb_records->remove_record($rec_index);
	}

	/**
	 * Remove a range of Palm Database records.
	 * @param $start_index start index of records.
	 * @param $end_index end index of records.
	 * @return true if removed.
	 */
	public function remove_pdb_records($start_index, $end_index)
	{
		return $this->pdb_records->remove_records($start_index, $end_index);
	}
}

/**
 * Palm Database header.
 */
class pdb_header
{
	const PDB_HEADER_LEN = 72;		/* Length of Palm Database 
						   Header */

	public $name;
	public $attributes;
	public $version;
	public $ctime;
	public $mtime;
	public $baktime;
	public $modnum;
	public $appinfo_id;
	public $sortinfo_id;
	public $type;
	public $creator;
	public $unique_id_seed;

	/**
         * Create new Palm Database header instance.
         */
	function __construct()
	{
		$this->_init();
	}

	/**
         * Initialise Palm Database header.
         */
	private function _init()
	{
		$this->set_name('');
		$this->attributes = 0;
		$this->version = 0;
		$this->set_ctime();
		$this->set_mtime();
		$this->baktime = 0;
		$this->modnum = 0;
		$this->appinfo_id = 0;
		$this->sortinfo_id = 0;
		$this->type = "";
		$this->creator = "";
		$this->unique_id_seed = 0;
	}

	/**
	 * Set name in Palm Database header.
	 * @param $name name.
	 */
	public function set_name($name)
	{
		$name = preg_replace('/[^-A-Za-z0-9 ]+/', '_', $name);
		$name = substr($name, 0, 31);
		$name = str_pad($name, 32, "\0");

		$this->name = $name;
	}

	/**
	 * Set creation date in Palm Database header to now.
	 */
	public function set_ctime()
	{
		$this->ctime = time();
	}

	/**
	 * Set modification date in Palm Database header to now.
	 */
	public function set_mtime()
	{
		$this->mtime = time();
	}

	/**
	 * Set type and creator in Palm Database header.
	 * @param $type type string.
	 * @param $creator creator string.
	 */
	public function set_type($type, $creator)
	{
		$this->type = $type;
		$this->creator = $creator;
	}

	/**
	 * Read Palm Database header from open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	public function read($pdb_f)
	{
		if (!is_resource($pdb_f))
			return 0;

		if ($h_buf = fread($pdb_f, self::PDB_HEADER_LEN))
		{
			$pdb_header = unpack("a32name/nattributes/nversion/Nctime/Nmtime/Nbaktime/Nmodnum/Nappinfo_id/Nsortinfo_id/a4type/a4creator/Nunique_id_seed", $h_buf);
			/* Name */
			if (isset($pdb_header['name']))
				$this->name = $pdb_header['name'];
			/* Attributes */
			if (isset($pdb_header['attributes']))
				$this->attributes = $pdb_header['attributes'];
			/* Version */
			if (isset($pdb_header['version']))
				$this->version = $pdb_header['version'];
			/* Creation Date */
			if (isset($pdb_header['ctime']))
				$this->ctime = $pdb_header['ctime'];
			/* Modification Date */
			if (isset($pdb_header['mtime']))
				$this->mtime = $pdb_header['mtime'];
			/* Last Backup Date */
			if (isset($pdb_header['baktime']))
				$this->baktime = $pdb_header['baktime'];
			/* Modification Number */
			if (isset($pdb_header['modnum']))
				$this->modnum = $pdb_header['modnum'];
			/* Application Info Offset */
			if (isset($pdb_header['appinfo_id']))
				$this->appinfo_id = $pdb_header['appinfo_id'];
			/* Sort Info Offset */
			if (isset($pdb_header['sortinfo_id']))
				$this->sortinfo_id = $pdb_header['sortinfo_id'];
			/* Type */
			if (isset($pdb_header['type']))
				$this->type = $pdb_header['type'];
			/* Creator */
			if (isset($pdb_header['creator']))
				$this->creator = $pdb_header['creator'];
			/* Unique ID Seed */
			if (isset($pdb_header['unique_id_seed']))
				$this->unique_id_seed = 
				   $pdb_header['unique_id_seed'];

			return 1;
		}

		return 0;
	}

	/**
	 * Write Palm Database header to open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	public function write($pdb_f)
	{
		if (!is_resource($pdb_f))
			return 0;

		$h_buf = pack("a32nnNNNNNNa4a4N",
		   $this->name,
		   $this->attributes,
		   $this->version,
		   $this->ctime,
		   $this->mtime,
		   $this->baktime,
		   $this->modnum,
		   $this->appinfo_id,
		   $this->sortinfo_id,
		   $this->type,
		   $this->creator,
		   $this->unique_id_seed
		);

		return fwrite($pdb_f, $h_buf);
	}

	/**
	 * Print Palm Database header.
	 */
	public function display()
	{
		echo "Name: $this->name\n";
		echo "Attributes: $this->attributes\n";
		echo "Version: $this->version\n";
		echo "CTime: $this->ctime\n";
		echo "MTime: $this->mtime\n";
		echo "BakTime: $this->baktime\n";
		echo "ModNum: $this->modnum\n";
		echo "AppInfo ID: $this->appinfo_id\n";
		echo "SortInfo ID: $this->sortinfo_id\n";
		echo "Type: $this->type\n";
		echo "Creator: $this->creator\n";
		echo "Unique ID Seed: $this->unique_id_seed\n";
	}
}

/**
 * Palm Database records.
 */
class pdb_records
{
	const PDB_RECORDS_HEADER_LEN = 6;	/* Length of Palm Database 
						   records header */

	public $file_size;
	public $next_record_id;
	public $num_records;
	public $record;

	/**
         * Create new Palm Database records instance.
         */
	function __construct()
	{
		$this->_init();
	}

	/**
         * Initialise Palm Database records.
         */
	private function _init()
	{
		$this->file_size = 0;
		$this->next_record_id = 0;
		$this->num_records = 0;
		$this->record = array();
	}

	/**
	 * Read Palm Database records from open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	public function read($pdb_f)
	{
		if (!is_resource($pdb_f))
			return 0;

		$stat = fstat($pdb_f);
		$this->file_size = $stat['size'];

		if ($this->_read_header($pdb_f))
			if ($this->_read_info($pdb_f))
				if ($this->_read_data($pdb_f))
					return 1;

		return 0;
	}

	/**
	 * Read Palm Database records header from open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	private function _read_header($pdb_f)
	{
		if ($recs_h_buf = fread($pdb_f, self::PDB_RECORDS_HEADER_LEN))
		{
			$pdb_records_h = unpack("Nnext_record_id/nnum_records", 
			   $recs_h_buf);

			/* Next Record ID */
			if (isset($pdb_records_h['next_record_id']))
				$this->next_record_id = 
				   $pdb_records_h['next_record_id'];
			/* Number of Records */
			if (isset($pdb_records_h['num_records']))
				$this->num_records = 
				   $pdb_records_h['num_records'];

			return 1;
		}

		return 0;
	}

	/**
	 * Read Palm Database records info from open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	private function _read_info($pdb_f)
	{
		for ($i = 0; $i < $this->num_records; $i++)
		{
			$pdb_record = new pdb_record();
			if (!$pdb_record->read($pdb_f))
				return 0;
			$this->record[$i] = $pdb_record;
		}

		return 1;
	}

	/**
	 * Read Palm Database records data from open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	private function _read_data($pdb_f)
	{
		for ($i = 0; $i < $this->num_records; $i++)
		{
			if (!isset($this->record[$i]))
				return 0;

			$offset = $this->record[$i]->record_offset;
			if (ftell($pdb_f) != $offset)
				fseek($pdb_f, $offset);
			
			if ($len = $this->data_len($i))
			{
				if ($data = fread($pdb_f, $len))
					$this->record[$i]->data = $data;
				else
					return 0;
			}
		}

		return 1;
	}

	/**
	 * Get the length of Palm Database record data.
	 * @param $index index of Palm Database record.
	 * @return length of Palm Database record data.
	 */
	public function data_len($index)
	{
		if ($index >= $this->num_records)
			return 0;

		if ($index < $this->num_records - 1)
			return $this->record[$index+1]->record_offset -
			   $this->record[$index]->record_offset;

		return $this->file_size - $this->record[$index]->record_offset;
	}

	/**
	 * Write Palm Database records to open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	public function write($pdb_f)
	{
		if (!is_resource($pdb_f))
			return 0;

		if ($this->_write_header($pdb_f))
			if ($this->_write_info($pdb_f))
				if (fwrite($pdb_f, pack("xx")))
					if ($this->_write_data($pdb_f))
						return 1;

		return 0;
	}

	/**
	 * Write Palm Database records header to open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	private function _write_header($pdb_f)
	{
		$recs_h_buf = pack("Nn",
		   $this->next_record_id,
		   $this->num_records
		);

		return fwrite($pdb_f, $recs_h_buf);
	}

	/**
	 * Write Palm Database records info to open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	private function _write_info($pdb_f)
	{
		for ($i = 0; $i < $this->num_records; $i++)
		{
			if (!isset($this->record[$i]))
				return 0;

			if (!$this->record[$i]->write($pdb_f))
				return 0;
		}

		return 1;
	}

	/**
	 * Write Palm Database records data to open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	private function _write_data($pdb_f)
	{
		for ($i = 0; $i < $this->num_records; $i++)
		{
			if (!isset($this->record[$i]))
				return 0;

			$offset = $this->record[$i]->record_offset;

			if (ftell($pdb_f) != $offset)
				fseek($pdb_f, $offset);
			
			if (!fwrite($pdb_f, $this->record[$i]->data))
				return 0;
		}

		return 1;
	}

	/**
	 * Add Palm Database record.
	 * @param $data data to add.
	 * @return palm database record.
	 */
	public function add_record($data)
	{
		$this->num_records++;
		$rec_index = $this->num_records - 1;

		$this->record[$rec_index] = new pdb_record();

		return $this->set_record($rec_index, $data);
	}

	/**
	 * Set Palm Database record.
	 * @param $rec_index index of record.
	 * @param $data data to set.
	 * @return palm database record.
	 */
	public function set_record($rec_index, $data)
	{
		if (($rec_index < 0) || ($rec_index > $this->num_records - 1))
			return null;

		$this->record[$rec_index]->data = $data;

		// calculate new record offsets
		$this->calc_record_offsets();

		return $this->record[$rec_index];
	}

	/**
	 * Remove Palm Database record.
	 * @param $rec_index index of record.
	 * @return true if removed.
	 */
	public function remove_record($rec_index)
	{
		return $this->remove_records($rec_index, $rec_index);
	}

	/**
	 * Remove a range of Palm Database records.
	 * @param $start_index start index of records.
	 * @param $end_index end index of records.
	 * @return true if removed.
	 */
	public function remove_records($start_index, $end_index)
	{
		if (($start_index < 0) || ($end_index < $start_index) || 
		   ($end_index >= $this->num_records))
			return false;

		for ($i = $start_index; $i <= $end_index; $i++)
			unset($this->record[$i]);
		$this->record = array_values($this->record);
		$this->num_records -= ($end_index - $start_index + 1);

		// calculate new record offsets
		$this->calc_record_offsets();

		return true;
	}

	/**
	 * Calculate record offsets.
	 * @param $start_index record index to start from.
	 */
	public function calc_record_offsets($start_index = 0)
	{
		for ($i = $start_index; $i < $this->num_records; $i++)
		{
			if ($i == 0)
				$this->record[0]->record_offset = 
				   $this->base_record_offset();
			else
				$this->record[$i]->record_offset =
				   $this->record[$i-1]->record_offset +
				   strlen($this->record[$i-1]->data);

			$this->record[$i]->unique_id = $i;
		}
	}

	/**
	 * Get record offset to start of data.
	 * @return base record offset.
	 */
	public function base_record_offset()
	{
		$record_offset = pdb_header::PDB_HEADER_LEN;
		$record_offset += pdb_records::PDB_RECORDS_HEADER_LEN;
		$record_offset += pdb_record::PDB_RECORD_INFO_LEN *
		   $this->num_records;
		$record_offset += 2; // gap to data

		return $record_offset;
	}

	/**
	 * Print Palm Database records header.
	 */
	public function display_header()
	{
		echo "File Size: $this->file_size\n";
		echo "Next Record ID: $this->next_record_id\n";
		echo "Num of Records: $this->num_records\n";
	}

	/**
	 * Print Palm Database records info.
	 */
	public function display_info()
	{
		for ($i = 0; $i < $this->num_records; $i++)
		{
			if (isset($this->record[$i]))
			{
				echo "Record Offset: ";
				echo $this->record[$i]->record_offset;
				echo "\n";
				echo "Record Attributes: ";
				echo $this->record[$i]->record_attributes;
				echo "\n";
				echo "Unique ID: ";
				echo $this->record[$i]->unique_id;
				echo "\n";
			}
		}
	}

	/**
	 * Print Palm Database records data.
	 */
	public function display_data()
	{
		for ($i = 0; $i < $this->num_records; $i++)
		{
			if (isset($this->record[$i]))
			{
				echo "Record $i\n";
				echo $this->record[$i]->data;
				echo "\n";
			}
		}
	}
}

/**
 * Palm Database record.
 */
class pdb_record
{
	const PDB_RECORD_INFO_LEN = 8;		/* Length of Palm Database 
						   record info */

	public $record_offset;
	public $record_attributes;
	public $unique_id;
	public $data;

	/**
         * Create new Palm Database record instance.
         */
	function __construct()
	{
		$this->_init();
	}

	/**
         * Initialise Palm Database record.
         */
	private function _init()
	{
		$this->record_offset = 0;
		$this->record_attributes = 0;
		$this->unique_id = 0;
		$this->data = "";
	}

	/**
	 * Read Palm Database record info from open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	public function read($pdb_f)
	{
		if (!is_resource($pdb_f))
			return 0;

		if ($info_buf = fread($pdb_f, self::PDB_RECORD_INFO_LEN))
		{
			$pdb_record_info = unpack("Nrecord_offset/Crecord_attributes/C3unique_id", $info_buf);
			/* Record Offset */
			if (isset($pdb_record_info['record_offset']))
				$this->record_offset = 
				   $pdb_record_info['record_offset'];
			/* Record Attributes */
			if (isset($pdb_record_info['record_attributes']))
				$this->record_attributes = 
				   $pdb_record_info['record_attributes'];
			/* Unique ID */
			if (isset($pdb_record_info['unique_id1']) &&
			   isset($pdb_record_info['unique_id2']) &&
			   isset($pdb_record_info['unique_id3']))
			{
				$this->unique_id = 
				   ($pdb_record_info['unique_id1'] << 16) |
				   ($pdb_record_info['unique_id2'] << 8) |
				   $pdb_record_info['unique_id3'];
			}

			return 1;
		}

		return 0;
	}

	/**
	 * Write Palm Database record info to open file stream.
	 * @param $pdb_f open file stream of PDB file.
	 * @return non-zero on success.
	 */
	public function write($pdb_f)
	{
		if (!is_resource($pdb_f))
			return 0;

		$unique_id1 = ($this->unique_id >> 16);
		$unique_id2 = ($this->unique_id >> 8);
		$unique_id3 = $this->unique_id;

		$info_buf = pack("NCCCC",
		   $this->record_offset,
		   $this->record_attributes,
		   $unique_id1,
		   $unique_id2,
		   $unique_id3
		);

		return fwrite($pdb_f, $info_buf);
	}
}

?>
