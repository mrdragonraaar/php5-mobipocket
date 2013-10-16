<?php
/**
 * exth_header.php
 * 
 * (c)2013 mrdragonraaar.com
 */
include_once('base_header.php');

/**
 * EXTH Header.
 */
class exth_header extends base_header
{
	const HEADER_TYPE_EXTH = "EXTH";	/* Header Type: EXTH */
	const EXTH_RECORD_HEADER_LEN = 8;	/* Length of EXTH record 
						   header */

	/* EXTH Record Types */
	const EXTH_RECORD_TYPE_DRMSERVERID = 1;		/* DRM Server ID */
	const EXTH_RECORD_TYPE_DRMCOMMERCEID = 2;	/* DRM Commerce ID */
	const EXTH_RECORD_TYPE_DRMEBOOKBASEID = 3;	/* DRM EBook Base ID */
	const EXTH_RECORD_TYPE_AUTHOR = 100;		/* Author */
	const EXTH_RECORD_TYPE_PUBLISHER = 101;		/* Publisher */
	const EXTH_RECORD_TYPE_IMPRINT = 102;		/* Imprint */
	const EXTH_RECORD_TYPE_DESCRIPTION = 103;	/* Description */
	const EXTH_RECORD_TYPE_ISBN = 104;		/* ISBN */
	const EXTH_RECORD_TYPE_SUBJECT = 105;		/* Subject */
	const EXTH_RECORD_TYPE_PUBLISHINGDATE = 106;	/* Publishing Date */
	const EXTH_RECORD_TYPE_REVIEW = 107;		/* Review */
	const EXTH_RECORD_TYPE_CONTRIBUTOR = 108;	/* Contributor */
	const EXTH_RECORD_TYPE_RIGHTS = 109;		/* Rights */
	const EXTH_RECORD_TYPE_SUBJECTCODE = 110;	/* Subject Code */
	const EXTH_RECORD_TYPE_TYPE = 111;		/* Type */
	const EXTH_RECORD_TYPE_SOURCE = 112;		/* Source */
	const EXTH_RECORD_TYPE_ASIN = 113;		/* ASIN */
	const EXTH_RECORD_TYPE_VERSIONNUMBER = 114;	/* Version Number */
	const EXTH_RECORD_TYPE_SAMPLE = 115;		/* Sample */
	const EXTH_RECORD_TYPE_STARTREADING = 116;	/* Start Reading */
	const EXTH_RECORD_TYPE_ADULT = 117;		/* Adult */
	const EXTH_RECORD_TYPE_RETAILPRICE = 118;	/* Retail Price */
	const EXTH_RECORD_TYPE_RETAILPRICECUR = 119;	/* Retail Price 
							   Currency */
	const EXTH_RECORD_TYPE_DICTSHORTNAME = 200;	/* Dictionary Short 
							   Name */
	const EXTH_RECORD_TYPE_COVEROFFSET = 201;	/* Cover Offset */
	const EXTH_RECORD_TYPE_THUMBOFFSET = 202;	/* Thumbnail Offset */
	const EXTH_RECORD_TYPE_HASFAKECOVER = 203;	/* Has Fake Cover */
	const EXTH_RECORD_TYPE_CREATORSOFTWARE = 204;	/* Creator Software */
	const EXTH_RECORD_TYPE_CREATORMAJOR = 205;	/* Creator Major 
							   Version */
	const EXTH_RECORD_TYPE_CREATORMINOR = 206;	/* Creator Minor 
							   Version */
	const EXTH_RECORD_TYPE_CREATORBUILD = 207;	/* Creator Build 
							   Version */
	const EXTH_RECORD_TYPE_WATERMARK = 208;		/* Watermark */
	const EXTH_RECORD_TYPE_TAMPERPROOF = 209;	/* Tamper Proof Keys */
	const EXTH_RECORD_TYPE_FONTSIGNATURE = 300;	/* Font Signature */
	const EXTH_RECORD_TYPE_CLIPPINGLIMIT = 401;	/* Clipping Limit */
	const EXTH_RECORD_TYPE_PUBLISHERLIMIT = 402;	/* Publisher Limit */
	const EXTH_RECORD_TYPE_TTSFLAG = 404;		/* Text to Speech 
							   enabled flag */
	const EXTH_RECORD_TYPE_CDETYPE = 501;		/* CDE Type */
	const EXTH_RECORD_TYPE_LASTUPDATETIME = 502;	/* Last updated time */
	const EXTH_RECORD_TYPE_UPDATEDTITLE = 503;	/* Updated Title */
	const EXTH_RECORD_TYPE_LANGUAGE = 524;		/* Language */

	/* Creator Software IDs */
	const CREATORSOFTWARE_MOBIGEN = 1;		/* mobigen */
	const CREATORSOFTWARE_MOBIPOCKETCREATOR = 2;	/* MobiPocket Creator */
	const CREATORSOFTWARE_KINDLEGEN_WINDOWS = 200;	/* kindlegen 
							   (Windows) */
	const CREATORSOFTWARE_KINDLEGEN_LINUX = 201;	/* kindlegen (Linux) */
	const CREATORSOFTWARE_KINDLEGEN_MAC = 202;	/* kindlegen (Mac) */
	const CREATORSOFTWARE_MOBIGEN_STR = "mobigen";
	const CREATORSOFTWARE_MOBIPOCKETCREATOR_STR = "MobiPocket Creator";
	const CREATORSOFTWARE_KINDLEGEN_WINDOWS_STR = "kindlegen (Windows)";
	const CREATORSOFTWARE_KINDLEGEN_LINUX_STR = "kindlegen (Linux)";
	const CREATORSOFTWARE_KINDLEGEN_MAC_STR = "kindlegen (Mac)";

	/* CDE Types */
	const CDETYPE_PERSONALDOC = "PDOC";	/* Personal Doc */
	const CDETYPE_EBOOK = "EBOK";		/* EBook */
	const CDETYPE_EBOOKSAMPLE = "EBSP";	/* EBook Sample */
	const CDETYPE_PERSONALDOC_STR = "Personal Doc";
	const CDETYPE_EBOOK_STR = "EBook";
	const CDETYPE_EBOOKSAMPLE_STR = "EBook Sample";

	public $record_count;
	public $record;

	/**
         * Create new EXTH header instance.
         */
	function __construct()
	{
		$this->_init();
	}

	/**
         * Initialise EXTH header.
         */
	protected function _init()
	{
		parent::_init();
		$this->record_count = 0;
		$this->record = array();
	}

	/**
	 * Get EXTH header from PDB record 0.
	 * @param $pdb_record_0 PDB record 0.
	 * @param $offset offset in PDB record 0.
	 * @return offset in PDB record 0.
	 */
	public function read($pdb_record_0, $offset)
	{
		$offset = parent::read($pdb_record_0, $offset);
		if ($offset > -1)
		{
			if ($this->is_header_type_exth())
			{
				$exth_header = unpack("Nrecord_count", 
				   $this->data);

				/* Record Count */
				if (isset($exth_header['record_count']))
					$this->record_count = 
					   $exth_header['record_count'];

				/* add padding to offset (multiple of 
				   4 bytes) */
				$offset += 4 - ($this->header_length % 4);

				if ($this->_read_records())
					return $offset;
			}
		}

		return -1;
	}

	/**
	 * Get EXTH records from EXTH record data.
	 * @return record count on success.
	 */
	private function _read_records()
	{
		if ($this->record_count <= 0)
			return 0;

		$record_offset = 4;
		for ($i = 0; $i < $this->record_count; $i++)
		{
			$record_offset = $this->_read_record($record_offset);
			if ($record_offset <= 0)
				return 0;
		}

		return $this->record_count;
	}

	/**
	 * Get single EXTH record from EXTH record data.
	 * @param $record_offset offset in EXTH record data.
	 * @return new record offset.
	 */
	private function _read_record($record_offset)
	{
		if ($record_header_buf = substr($this->data, $record_offset,
		   self::EXTH_RECORD_HEADER_LEN))
		{
			$exth_record = new exth_record();

			$record_header = unpack("Nrecord_type/Nrecord_length",
			   $record_header_buf);

			/* Record Type */
			if (isset($record_header['record_type']))
				$exth_record->record_type = 
				   $record_header['record_type'];
			/* Record Length */
			if (isset($record_header['record_length']))
				$exth_record->record_length = 
				   $record_header['record_length'];

			$record_offset += self::EXTH_RECORD_HEADER_LEN;
			$record_length = $exth_record->record_length - 
			   self::EXTH_RECORD_HEADER_LEN;

			if ($record_length > 0)
				$exth_record->record_data = 
				   substr($this->data, $record_offset, 
				   $record_length);

			$this->record[] = $exth_record;

			$record_offset += $record_length;
			return $record_offset;
		}

		return -1;
	}

	/**
	 * Check if header is EXTH header type.
	 * @return non-zero if header is EXTH header type.
	 */
	public function is_header_type_exth()
	{
		return $this->is_header_type(self::HEADER_TYPE_EXTH);
	}

	/**
	 * Get EXTH record data of first record from EXTH records array
	 *    that matches record type.
	 * @param $record_type EXTH record type.
	 * @return record data string.
	 */
	private function exth_record($record_type)
	{
		for ($i = 0; $i < $this->record_count; $i++)
			if ($this->record[$i]->record_type == $record_type)
				return $this->record[$i]->record_data;

		return "";
	}

	/**
	 * Get EXTH record data of records from EXTH records array
	 *    that matches record type.
	 * @param $record_type EXTH record type.
	 * @return record data array.
	 */
	private function exth_records($record_type)
	{
		$record_data = array();

		for ($i = 0; $i < $this->record_count; $i++)
			if ($this->record[$i]->record_type == $record_type)
				$record_data[] = $this->record[$i]->record_data;

		return $record_data;
	}

	/**
	 * Get EXTH record data as uint32 of first record from EXTH records 
	 *    array that matches record type.
	 * @param $record_type EXTH record type.
	 * @return record data uint32.
	 */
	private function exth_record_l($record_type)
	{
		if ($record_data_str = $this->exth_record($record_type))
		{
			$record_data = unpack("Nrecord_data", $record_data_str);
			if (isset($record_data['record_data']))
				return $record_data['record_data'];
		}

		return 0;
	}

	/**
	 * Get author from EXTH records.
	 * @return author.
	 */
	public function author()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_AUTHOR);
	}

	/**
	 * Get authors from EXTH records.
	 * @return array of authors.
	 */
	public function authors()
	{
		return $this->exth_records(self::EXTH_RECORD_TYPE_AUTHOR);
	}

	/**
	 * Get publisher from EXTH records.
	 * @return publisher.
	 */
	public function publisher()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_PUBLISHER);
	}

	/**
	 * Get imprint from EXTH records.
	 * @return imprint.
	 */
	public function imprint()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_IMPRINT);
	}

	/**
	 * Get description from EXTH records.
	 * @return description.
	 */
	public function description()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_DESCRIPTION);
	}

	/**
	 * Get ISBN from EXTH records.
	 * @return ISBN.
	 */
	public function isbn()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_ISBN);
	}

	/**
	 * Get subject from EXTH records.
	 * @return subject.
	 */
	public function subject()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_SUBJECT);
	}

	/**
	 * Get subjects from EXTH records.
	 * @return array of subjects.
	 */
	public function subjects()
	{
		return $this->exth_records(self::EXTH_RECORD_TYPE_SUBJECT);
	}

	/**
	 * Get publishing date from EXTH records.
	 * @return publishing date.
	 */
	public function publishing_date()
	{
		return $this->exth_record(
		   self::EXTH_RECORD_TYPE_PUBLISHINGDATE);
	}

	/**
         * Get the publishing date from EXTH records as UTC.
         * Will only return result if date after 1901-12-13.
	 * @return publishing date as UTC.
         */
	public function publishing_date_utc()
	{
		$publish_date = $this->publishing_date();

		return $publish_date ? strtotime($publish_date) : -1;
	}

	/**
         * Get the publishing date from EXTH records as formatted string.
	 * @param $format date format.
	 * @return publishing date as formatted string.
         */
	public function publishing_date_str($format = 'F d Y')
	{
		$publish_date = $this->publishing_date();

		$date = date_create($publish_date);

		return $date ? date_format($date, $format) : "";
	}

	/**
	 * Get review from EXTH records.
	 * @return review.
	 */
	public function review()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_REVIEW);
	}

	/**
	 * Get contributor from EXTH records.
	 * @return contributor.
	 */
	public function contributor()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_CONTRIBUTOR);
	}

	/**
	 * Get rights from EXTH records.
	 * @return rights.
	 */
	public function rights()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_RIGHTS);
	}

	/**
	 * Get asin from EXTH records.
	 * @return asin.
	 */
	public function asin()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_ASIN);
	}

	/**
	 * Get retail price from EXTH records.
	 * @return retail price.
	 */
	public function retail_price()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_RETAILPRICE);
	}

	/**
	 * Get retail price currency from EXTH records.
	 * @return retail price currency.
	 */
	public function retail_price_currency()
	{
		return $this->exth_record(
		   self::EXTH_RECORD_TYPE_RETAILPRICECUR);
	}

	/**
	 * Get dictionary short name from EXTH records.
	 * @return dictionary short name.
	 */
	public function dictionary_short_name()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_DICTSHORTNAME);
	}

	/**
	 * Get cover offset from EXTH records.
	 * @return cover offset.
	 */
	public function cover_offset()
	{
		return $this->exth_record_l(self::EXTH_RECORD_TYPE_COVEROFFSET);
	}

	/**
	 * Get thumbnail offset from EXTH records.
	 * @return thumbnail offset.
	 */
	public function thumbnail_offset()
	{
		return $this->exth_record_l(self::EXTH_RECORD_TYPE_THUMBOFFSET);
	}

	/**
	 * Get creator software from EXTH records.
	 * @return creator software.
	 */
	public function creator_software()
	{
		return $this->exth_record_l(
		   self::EXTH_RECORD_TYPE_CREATORSOFTWARE);
	}

	/**
	 * Get creator software from EXTH records as string.
	 * @return creator software string.
	 */
	public function creator_software_str()
	{
		switch($this->creator_software())
		{
			case self::CREATORSOFTWARE_MOBIGEN:
				return self::CREATORSOFTWARE_MOBIGEN_STR;
			case self::CREATORSOFTWARE_MOBIPOCKETCREATOR:
				return 
				   self::CREATORSOFTWARE_MOBIPOCKETCREATOR_STR;
			case self::CREATORSOFTWARE_KINDLEGEN_WINDOWS:
				return 
				   self::CREATORSOFTWARE_KINDLEGEN_WINDOWS_STR;
			case self::CREATORSOFTWARE_KINDLEGEN_LINUX:
				return 
				   self::CREATORSOFTWARE_KINDLEGEN_LINUX_STR;
			case self::CREATORSOFTWARE_KINDLEGEN_MAC:
				return self::CREATORSOFTWARE_KINDLEGEN_MAC_STR;
			default:
				return "";
		}
	}

	/**
	 * Get creator major from EXTH records.
	 * @return creator major.
	 */
	public function creator_major()
	{
		return $this->exth_record_l(
		   self::EXTH_RECORD_TYPE_CREATORMAJOR);
	}

	/**
	 * Get creator minor from EXTH records.
	 * @return creator minor.
	 */
	public function creator_minor()
	{
		return $this->exth_record_l(
		   self::EXTH_RECORD_TYPE_CREATORMINOR);
	}

	/**
	 * Get creator build from EXTH records.
	 * @return creator build.
	 */
	public function creator_build()
	{
		return $this->exth_record_l(
		   self::EXTH_RECORD_TYPE_CREATORBUILD);
	}

	/**
	 * Get cde type from EXTH records.
	 * @return cde type.
	 */
	public function cde_type()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_CDETYPE);
	}

	/**
	 * Get cde type from EXTH records as string.
	 * @return cde type string.
	 */
	public function cde_type_str()
	{
		switch($this->cde_type())
		{
			case self::CDETYPE_PERSONALDOC:
				return self::CDETYPE_PERSONALDOC_STR;
			case self::CDETYPE_EBOOK:
				return self::CDETYPE_EBOOK_STR;
			case self::CDETYPE_EBOOKSAMPLE:
				return self::CDETYPE_EBOOKSAMPLE_STR;
			default:
				return "";
		}
	}

	/**
	 * Get updated title from EXTH records.
	 * @return updated title.
	 */
	public function updated_title()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_UPDATEDTITLE);
	}

	/**
	 * Get language from EXTH records.
	 * @return language.
	 */
	public function language()
	{
		return $this->exth_record(self::EXTH_RECORD_TYPE_LANGUAGE);
	}

	/**
	 * Print EXTH header.
	 */
	public function display()
	{
		parent::display();
		echo "Record Count: $this->record_count\n";
		for ($i = 0; $i < $this->record_count; $i++)
		{
			$this->record[$i]->display();
		}
	}
}

/**
 * EXTH Record.
 */
class exth_record
{
	public $record_type;
	public $record_length;
	public $record_data;

	/**
         * Create new EXTH record instance.
         */
	function __construct()
	{
		$this->_init();
	}

	/**
         * Initialise EXTH record.
         */
	private function _init()
	{
		$this->record_type = 0;
		$this->record_length = 0;
		$this->record_data = "";
	}

	/**
	 * Print EXTH record.
	 */
	public function display()
	{
		echo "Record Type: $this->record_type\n";
		echo "Record Length: $this->record_length\n";
		echo "Record Data: $this->record_data\n";
	}
}

?>
