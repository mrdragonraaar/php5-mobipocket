<?php
/**
 * mobipocket.php
 * 
 * (c)2013 mrdragonraaar.com
 */
include_once('palmdoc.php');
include_once('mobi_header.php');
include_once('exth_header.php');

/**
 * MOBIPocket.
 */
class mobipocket extends palmdoc
{
	public $mobi_header;
	public $exth_header;
	public $full_name;

	/**
         * Create new MOBIPocket instance.
         */
	function __construct()
	{
		$this->_init();
	}

	/**
         * Initialise MOBIPocket.
         */
	protected function _init()
	{
		parent::_init();
		$this->mobi_header = new mobi_header();
		$this->exth_header = new exth_header();
		$this->full_name = "";
	}

	/**
	 * Read MOBIPocket from open file stream.
	 * @param $mobipocket_f open file stream of MOBIPocket file.
	 * @return non-zero on success.
	 */
	public function read($mobipocket_f)
	{
		$this->_init();

		if (!parent::read($mobipocket_f))
			return 0;

		if ($this->is_mobipocket())
		{
			$pdb_record_0 = $this->pdb_records->record[0]->data;

			/* MOBI Header */
			$offset =  $this->mobi_header->read(
			   $pdb_record_0, palmdoc_header::PALMDOC_HEADER_LEN);
			if ($offset <= 0)
				return 0;

			/* EXTH Header */
			if ($this->mobi_header->has_exth_header())
			{
				$offset = $this->exth_header->read(
				   $pdb_record_0, $offset);
				if ($offset <= 0)
					return 0;
			}

			$this->_read_full_name();
		}

		return 1;
	}

	/**
	 * Check type and creator of Palm Database is for MOBIPocket.
	 * @return non-zero if type and creator match MOBIPocket.
	 */
	public function is_mobipocket()
	{
		return $this->is_pdb_type(self::PDB_TYPE_MOBI, 
		   self::PDB_CREATOR_MOBI);
	}

	/**
	 * Get full name from PDB record 0.
	 * @return new offset in PDB record 0.
	 */
	private function _read_full_name()
	{
		$pdb_record_0 = $this->pdb_records->record[0]->data;
		$offset = $this->mobi_header->full_name_offset;
		$length = $this->mobi_header->full_name_length;

		if ($offset && $length)
		{
			$this->full_name = substr($pdb_record_0, $offset, 
			   $length);

			return $offset + $length;
		}

		return -1;
	}

	/**
	 * Map property methods from mobi_header and exth_header.
	 * @param $method method name.
	 * @param $arguments method arguments. Not used.
	 * @return method return value.
	 */
	public function __call($method, $arguments)
	{
		if (method_exists($this->mobi_header, $method))
			return $this->mobi_header->$method();

		if (method_exists($this->exth_header, $method))
			return $this->exth_header->$method();

		return;
	}

	/**
	 * Mapping to exth_header->publishing_date_str().
	 */
	public function publishing_date_str($format = 'F d Y')
	{
		return $this->exth_header->publishing_date_str($format);
	}

	/**
	 * Get title.
	 * Returns full name if updated title does not exist.
	 * @return title.
	 */
	public function title()
	{
		if ($this->updated_title())
			return $this->updated_title();

		return $this->full_name;
	}

	/**
	 * Get the cover image data.
	 * @return cover image data.
	 */
	public function cover()
	{
		return $this->image_record($this->cover_index());
	}

	/**
	 * Get the length of the cover image data.
	 * @return cover image data length.
	 */
	public function cover_length()
	{
		return $this->image_record_length($this->cover_index());
	}

	/**
	 * Get the index of the cover image data in the Palm Database 
	 *    records.
	 * @return cover image data index.
	 */
	public function cover_index()
	{
		return $this->image_record_offset2index($this->cover_offset());
	}

	/**
	 * Get the thumbnail image data.
	 * @return thumbnail image data.
	 */
	public function thumbnail()
	{
		return $this->image_record($this->thumbnail_index());
	}

	/**
	 * Get the length of the thumbnail image data.
	 * @return thumbnail image data length.
	 */
	public function thumbnail_length()
	{
		return $this->image_record_length($this->thumbnail_index());
	}

	/**
	 * Get the index of the thumbnail image data in the Palm Database 
	 *    records.
	 * @return thumbnail image data index.
	 */
	public function thumbnail_index()
	{
		return $this->image_record_offset2index(
		   $this->thumbnail_offset());
	}

	/**
	 * Get the data of the image record at specified index.
	 * @param $index index of image record.
	 * @return image data.
	 */
	public function image_record($index)
	{
		/* Not a image record */
		if (!$this->image_record_length($index))
			return "";

		return $this->pdb_records->record[$index]->data;
	}

	/**
	 * Get the length of the image record at specified index.
	 * @param $index index of image record.
	 * @return image record data length.
	 */
	public function image_record_length($index)
	{
		/* Not a image record */
		if (!$this->is_image_record_index($index))
			return 0;

		return $this->pdb_records->data_len($index);
	}

	/**
	 * Get the index of image in the Palm Database records from offset.
	 * @param $offset offset of image record.
	 * @return image data index.
	 */
	public function image_record_offset2index($offset)
	{
		$index = $this->mobi_header->first_image_index + $offset;
		if ($this->is_image_record_index($index))
			return $index;

		return 0;
	}

	/**
	 * Check if specified index is a valid image record index.
	 * @param $index index of image record.
	 * @return non-zero if valid image record index.
	 */
	public function is_image_record_index($index)
	{
		$start_index = $this->mobi_header->first_image_index;
		$end_index = $this->mobi_header->last_content_index;

		return (($start_index > 0) && ($end_index >= $start_index) && 
		   ($index >= $start_index) && ($index <= $end_index));
	}
}

?>
