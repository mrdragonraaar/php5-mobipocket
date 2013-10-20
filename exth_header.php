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
	const EXTH_HEADER_LEN = 12;		/* Initial header length */
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
		$this->identifier = self::HEADER_TYPE_EXTH;
		$this->header_length = self::EXTH_HEADER_LEN;
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

				if ($this->_read_records() > -1)
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
				return -1;
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
	 * Get packed EXTH header.
	 * @return packed EXTH header.
	 */
	public function write()
	{
		$this->data = pack("N", $this->record_count);

		for ($i = 0; $i < $this->record_count; $i++)
		{
			$this->data .= pack("NN",
			   $this->record[$i]->record_type,
			   $this->record[$i]->record_length);
			$this->data .= $this->record[$i]->record_data;
		}

		/* add padding to offset (multiple of 
		   4 bytes) */
		$padding = 4 - ($this->header_length % 4);
		$this->data .= pack("x$padding");

		return parent::write();
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
	 * Add EXTH record.
	 * @param $record_type EXTH record type.
	 * @param $record_data record data.
	 * @return EXTH record.
	 */
	public function add_exth_record($record_type, $record_data)
	{
		$rec_index = $this->record_count;

		$this->record[$rec_index] = new exth_record();
		$this->record_count++;

		return $this->_set_exth_record($rec_index, $record_type, $record_data);
	}

	/**
	 * Set EXTH record data.
	 * @param $record_type EXTH record type.
	 * @param $record_data record data.
	 * @return EXTH record.
	 */
	public function set_exth_record($record_type, $record_data)
	{
		$rec_index = $this->exth_record_index($record_type);
		if ($rec_index < 0)
			return $this->add_exth_record($record_type, $record_data);

		return $this->_set_exth_record($rec_index, $record_type, $record_data);
	}

	/**
	 * Set EXTH uint32 record data.
	 * @param $record_type EXTH record type.
	 * @param $record_data record data.
	 * @return EXTH record.
	 */
	public function set_exth_record_l($record_type, $record_data)
	{
		$record_data = pack("N", $record_data);

		return $this->set_exth_record($record_type, $record_data);
	}

	/**
	 * Set EXTH record data at specified index.
	 * @param $rec_index record index.
	 * @param $record_type EXTH record type.
	 * @param $record_data record data.
	 * @return EXTH record.
	 */
	private function _set_exth_record($rec_index, $record_type, $record_data)
	{
		if (isset($this->record[$rec_index]))
		{
			$exth_record = $this->record[$rec_index];

			$record_length = strlen($record_data) +
			   self::EXTH_RECORD_HEADER_LEN;

			$this->header_length -= $exth_record->record_length;
			$this->header_length += $record_length;

			$exth_record->record_type = $record_type;
			$exth_record->record_length = $record_length;
			$exth_record->record_data = $record_data;

			return $this->record[$rec_index] = $exth_record;
		}

		return null;
	}

	/**
	 * Remove all EXTH records of specified record type.
	 * @param $record_type EXTH record type.
	 */
	public function remove_exth_record($record_type)
	{
		$rec_index = $this->exth_record_index($record_type);
		while ($rec_index > -1)
		{
			$this->record_count--;
			$this->header_length -= $this->record[$rec_index]->record_length;
			unset($this->record[$rec_index]);
			$this->record = array_values($this->record);

			$rec_index = $this->exth_record_index($record_type);
		}
	}

	/**
	 * Get EXTH record index of first record from EXTH records array
	 *    that matches record type.
	 * @param $record_type EXTH record type.
	 * @return record index.
	 */
	private function exth_record_index($record_type)
	{
		for ($i = 0; $i < $this->record_count; $i++)
			if ($this->record[$i]->record_type == $record_type)
				return $i;

		return -1;
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
	 * Add author to EXTH records.
	 * @param $author author.
	 * @return author record.
	 */
	public function add_author($author)
	{
		return $this->add_exth_record(self::EXTH_RECORD_TYPE_AUTHOR, $author);
	}

	/**
	 * Remove all authors from EXTH records.
	 */
	public function remove_author()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_AUTHOR);
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
	 * Set publisher in EXTH records.
	 * @param $publisher publisher.
	 * @return publisher record.
	 */
	public function set_publisher($publisher)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_PUBLISHER, $publisher);
	}

	/**
	 * Remove publisher from EXTH records.
	 */
	public function remove_publisher()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_PUBLISHER);
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
	 * Set imprint in EXTH records.
	 * @param $imprint imprint.
	 * @return imprint record.
	 */
	public function set_imprint($imprint)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_IMPRINT, $imprint);
	}

	/**
	 * Remove imprint from EXTH records.
	 */
	public function remove_imprint()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_IMPRINT);
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
	 * Set description in EXTH records.
	 * @param $description description.
	 * @return description record.
	 */
	public function set_description($description)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_DESCRIPTION, $description);
	}

	/**
	 * Remove description from EXTH records.
	 */
	public function remove_description()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_DESCRIPTION);
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
	 * Set isbn in EXTH records.
	 * @param $isbn isbn.
	 * @return isbn record.
	 */
	public function set_isbn($isbn)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_ISBN, $isbn);
	}

	/**
	 * Remove isbn from EXTH records.
	 */
	public function remove_isbn()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_ISBN);
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
	 * Add subject to EXTH records.
	 * @param $subject subject.
	 * @return subject record.
	 */
	public function add_subject($subject)
	{
		return $this->add_exth_record(self::EXTH_RECORD_TYPE_SUBJECT, $subject);
	}

	/**
	 * Remove all subjects from EXTH records.
	 */
	public function remove_subject()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_SUBJECT);
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
	 * Set publishing date in EXTH records.
	 * @param $publishing_date publishing date.
	 * @return publishing data record.
	 */
	public function set_publishing_date($publishing_date)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_PUBLISHINGDATE, $publishing_date);
	}

	/**
	 * Remove publishing date from EXTH records.
	 */
	public function remove_publishing_date()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_PUBLISHINGDATE);
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
         * Set the publishing date in EXTH records from formatted string.
	 * @param $publishing_date_str string representation of publishing date.
	 * @return publishing data record.
         */
	public function set_publishing_date_str($publishing_date_str)
	{
		$date = date_create($publishing_date_str);

		if (!$date)
			return null;

		$publish_date = date_format($date, 'c');

		return $this->set_publishing_date($publish_date);
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
	 * Set review in EXTH records.
	 * @param $review review.
	 * @return review record.
	 */
	public function set_review($review)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_REVIEW, $review);
	}

	/**
	 * Remove review from EXTH records.
	 */
	public function remove_review()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_REVIEW);
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
	 * Set contributor in EXTH records.
	 * @param $contributor contributor.
	 * @return contributor record.
	 */
	public function set_contributor($contributor)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_CONTRIBUTOR, $contributor);
	}

	/**
	 * Remove contributor from EXTH records.
	 */
	public function remove_contributor()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_CONTRIBUTOR);
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
	 * Set rights in EXTH records.
	 * @param $rights rights.
	 * @return rights record.
	 */
	public function set_rights($rights)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_RIGHTS, $rights);
	}

	/**
	 * Remove rights from EXTH records.
	 */
	public function remove_rights()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_RIGHTS);
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
	 * Set asin in EXTH records.
	 * @param $asin asin.
	 * @return asin record.
	 */
	public function set_asin($asin)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_ASIN, $asin);
	}

	/**
	 * Remove asin from EXTH records.
	 */
	public function remove_asin()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_ASIN);
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
	 * Set retail price in EXTH records.
	 * @param $retail_price retail price.
	 * @return retail price record.
	 */
	public function set_retail_price($retail_price)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_RETAILPRICE, $retail_price);
	}

	/**
	 * Remove retail price from EXTH records.
	 */
	public function remove_retail_price()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_RETAILPRICE);
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
	 * Set retail price currency in EXTH records.
	 * @param $retail_price_currency retail price currency.
	 * @return retail price currency record.
	 */
	public function set_retail_price_currency($retail_price_currency)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_RETAILPRICECUR,
		   $retail_price_currency);
	}

	/**
	 * Remove retail price currency from EXTH records.
	 */
	public function remove_retail_price_currency()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_RETAILPRICECUR);
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
	 * Set dictionary short name in EXTH records.
	 * @param $dictionary_short_name dictionary short name.
	 * @return dictionary short name record.
	 */
	public function set_dictionary_short_name($dictionary_short_name)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_DICTSHORTNAME,
		   $dictionary_short_name);
	}

	/**
	 * Remove dictionary short name from EXTH records.
	 */
	public function remove_dictionary_short_name()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_DICTSHORTNAME);
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
	 * Set cover offset in EXTH records.
	 * @param $cover_offset cover offset.
	 * @return cover offset record.
	 */
	public function set_cover_offset($cover_offset)
	{
		return $this->set_exth_record_l(self::EXTH_RECORD_TYPE_COVEROFFSET,
		   $cover_offset);
	}

	/**
	 * Remove cover offset from EXTH records.
	 */
	public function remove_cover_offset()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_COVEROFFSET);
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
	 * Set thumbnail offset in EXTH records.
	 * @param $thumbnail_offset thumbnail offset.
	 * @return thumbnail offset record.
	 */
	public function set_thumbnail_offset($thumbnail_offset)
	{
		return $this->set_exth_record_l(self::EXTH_RECORD_TYPE_THUMBOFFSET,
		   $thumbnail_offset);
	}

	/**
	 * Remove thumbnail offset from EXTH records.
	 */
	public function remove_thumbnail_offset()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_THUMBOFFSET);
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
	 * Set creator software to mobigen in EXTH records.
	 * @return creator software record.
	 */
	public function set_creator_software_mobigen()
	{
		return $this->set_creator_software(self::CREATORSOFTWARE_MOBIGEN);
	}

	/**
	 * Set creator software to MobiPocket Creator in EXTH records.
	 * @return creator software record.
	 */
	public function set_creator_software_mobipocket_creator()
	{
		return $this->set_creator_software(self::CREATORSOFTWARE_MOBIPOCKETCREATOR);
	}

	/**
	 * Set creator software to kindlegen (Windows) in EXTH records.
	 * @return creator software record.
	 */
	public function set_creator_software_kindlegen_windows()
	{
		return $this->set_creator_software(self::CREATORSOFTWARE_KINDLEGEN_WINDOWS);
	}

	/**
	 * Set creator software to kindlegen (Linux) in EXTH records.
	 * @return creator software record.
	 */
	public function set_creator_software_kindlegen_linux()
	{
		return $this->set_creator_software(self::CREATORSOFTWARE_KINDLEGEN_LINUX);
	}

	/**
	 * Set creator software to kindlegen (Mac) in EXTH records.
	 * @return creator software record.
	 */
	public function set_creator_software_kindlegen_mac()
	{
		return $this->set_creator_software(self::CREATORSOFTWARE_KINDLEGEN_MAC);
	}

	/**
	 * Set creator software in EXTH records.
	 * @param $creator_software creator software.
	 * @return creator software record.
	 */
	public function set_creator_software($creator_software)
	{
		return $this->set_exth_record_l(self::EXTH_RECORD_TYPE_CREATORSOFTWARE,
		   $creator_software);
	}

	/**
	 * Remove creator software from EXTH records.
	 */
	public function remove_creator_software()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_CREATORSOFTWARE);
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
	 * Set creator major in EXTH records.
	 * @param $creator_major creator major.
	 * @return creator major record.
	 */
	public function set_creator_major($creator_major)
	{
		return $this->set_exth_record_l(self::EXTH_RECORD_TYPE_CREATORMAJOR,
		   $creator_major);
	}

	/**
	 * Remove creator major from EXTH records.
	 */
	public function remove_creator_major()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_CREATORMAJOR);
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
	 * Set creator minor in EXTH records.
	 * @param $creator_minor creator minor.
	 * @return creator minor record.
	 */
	public function set_creator_minor($creator_minor)
	{
		return $this->set_exth_record_l(self::EXTH_RECORD_TYPE_CREATORMINOR,
		   $creator_minor);
	}

	/**
	 * Remove creator minor from EXTH records.
	 */
	public function remove_creator_minor()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_CREATORMINOR);
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
	 * Set creator build in EXTH records.
	 * @param $creator_build creator build.
	 * @return creator build record.
	 */
	public function set_creator_build($creator_build)
	{
		return $this->set_exth_record_l(self::EXTH_RECORD_TYPE_CREATORBUILD,
		   $creator_build);
	}

	/**
	 * Remove creator build from EXTH records.
	 */
	public function remove_creator_build()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_CREATORBUILD);
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
	 * Set cde type in EXTH records.
	 * @param $cde_type cde type.
	 * @return cde type record.
	 */
	public function set_cde_type($cde_type)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_CDETYPE,
		   $cde_type);
	}

	/**
	 * Remove cde type from EXTH records.
	 */
	public function remove_cde_type()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_CDETYPE);
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
	 * Set updated title in EXTH records.
	 * @param $updated_title updated title.
	 * @return updated title record.
	 */
	public function set_updated_title($updated_title)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_UPDATEDTITLE,
		   $updated_title);
	}

	/**
	 * Remove updated title from EXTH records.
	 */
	public function remove_updated_title()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_UPDATEDTITLE);
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
	 * Set language in EXTH records.
	 * @param $language language.
	 * @return language record.
	 */
	public function set_language($language)
	{
		return $this->set_exth_record(self::EXTH_RECORD_TYPE_LANGUAGE,
		   $language);
	}

	/**
	 * Remove language from EXTH records.
	 */
	public function remove_language()
	{
		return $this->remove_exth_record(self::EXTH_RECORD_TYPE_LANGUAGE);
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
