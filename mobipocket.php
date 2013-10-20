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
		$this->update_pdb_record_0();
	}

	/**
         * Initialise MOBIPocket.
         */
	protected function _init()
	{
		parent::_init();
		$this->set_mobipocket_type();
		$this->mobi_header = new mobi_header();
		$this->exth_header = new exth_header();
		$this->full_name = "";
	}

	/**
         * Get MOBIPocket Record 0 data from MOBIPocket and EXTH headers.
         */
	protected function _pdb_record_0_data()
	{
		// remove exth header flag if no records exist.
		if ($this->exth_header->record_count > 0)
			$this->mobi_header->set_exth_flag_header();
		else
			$this->mobi_header->set_exth_flag_none();

		// update offset for full name.
		$this->update_full_name_offset();

		$data = parent::_pdb_record_0_data() .
		   $this->mobi_header->write();

		if ($this->mobi_header->has_exth_header() &&
		   $this->exth_header->record_count > 0)
			$data .= $this->exth_header->write();

		if ($this->mobi_header->full_name_length > 0)
			$data .= $this->write_full_name();

		return $data;
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
	 * Set type and creator of Palm Database to MOBIPocket.
	 */
	public function set_mobipocket_type()
	{
		$this->set_pdb_type(self::PDB_TYPE_MOBI, 
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
	 * Set full name in PDB record 0.
	 * @param $full_name full name.
	 * @return full name.
	 */
	public function set_full_name($full_name)
	{
		$this->update_full_name_offset();
		$this->mobi_header->full_name_length = strlen($full_name);

		return $this->full_name = $full_name;
	}

	/**
	 * Update offset for full name.
	 * @return new full name offset.
	 */
	public function update_full_name_offset()
	{
		$offset = palmdoc_header::PALMDOC_HEADER_LEN +
		   $this->mobi_header->header_length;

		if ($this->mobi_header->has_exth_header() &&
		   $this->exth_header->record_count > 0)
		{
			$offset += $this->exth_header->header_length +
			   (4 - ($this->exth_header->header_length % 4));
		}

		return $this->mobi_header->full_name_offset = $offset;
	}

	/**
	 * Get full name for writing to PDB record 0.
	 * @return PDB record 0 full name.
	 */
	public function write_full_name()
	{
		$len = $this->mobi_header->full_name_length + 2;
		$padding = 4 - ($len % 4);
		$len = $len + $padding;

		return str_pad($this->full_name, $len, "\0");
	}

	/**
	 * EXTH header property method mappings.
	 */
	/* Author */
	public function author() { return $this->exth_header->author(); }
	public function authors() { return $this->exth_header->authors(); }
	public function add_author($author) { return $this->exth_header->add_author($author); }
	public function remove_author() { return $this->exth_header->remove_author(); }

	/* Publisher */
	public function publisher() { return $this->exth_header->publisher(); }
	public function set_publisher($publisher) { return $this->exth_header->set_publisher($publisher); }
	public function remove_publisher() { return $this->exth_header->remove_publisher(); }

	/* Imprint */
	public function imprint() { return $this->exth_header->imprint(); }
	public function set_imprint($imprint) { return $this->exth_header->set_imprint($imprint); }
	public function remove_imprint() { return $this->exth_header->remove_imprint(); }

	/* Description */
	public function description() { return $this->exth_header->description(); }
	public function set_description($description)
	{ return $this->exth_header->set_description($description); }
	public function remove_description() { return $this->exth_header->remove_description(); }

	/* ISBN */
	public function isbn() { return $this->exth_header->isbn(); }
	public function set_isbn($isbn) { return $this->exth_header->set_isbn($isbn); }
	public function remove_isbn() { return $this->exth_header->remove_isbn(); }

	/* Subject */
	public function subject() { return $this->exth_header->subject(); }
	public function subjects() { return $this->exth_header->subjects(); }
	public function add_subject($subject) { return $this->exth_header->add_subject($subject); }
	public function remove_subject() { return $this->exth_header->remove_subject(); }

	/* Publishing Date */
	public function publishing_date() { return $this->exth_header->publishing_date(); }
	public function set_publishing_date($publishing_date)
	{ return $this->exth_header->set_publishing_date($publishing_date); }
	public function remove_publishing_date()
	{ return $this->exth_header->remove_publishing_date(); }
	public function publishing_date_utc() { $this->exth_header->publishing_date_utc(); }
	public function publishing_date_str($format = 'F d Y')
	{ return $this->exth_header->publishing_date_str($format); }

	/* Review */
	public function review() { return $this->exth_header->review(); }
	public function set_review($review) { return $this->exth_header->set_review($review); }
	public function remove_review() { return $this->exth_header->remove_review(); }

	/* Contributor */
	public function contributor() { return $this->exth_header->contributor(); }
	public function set_contributor($contributor)
	{ return $this->exth_header->set_contributor($contributor); }
	public function remove_contributor() { return $this->exth_header->remove_contributor(); }

	/* Rights */
	public function rights() { return $this->exth_header->rights(); }
	public function set_rights($rights) { return $this->exth_header->set_rights($rights); }
	public function remove_rights() { return $this->exth_header->remove_rights(); }

	/* ASIN */
	public function asin() { return $this->exth_header->asin(); }
	public function set_asin($asin) { return $this->exth_header->set_asin($asin); }
	public function remove_asin() { return $this->exth_header->remove_asin(); }

	/* Retail Price */
	public function retail_price() { return $this->exth_header->retail_price(); }
	public function set_retail_price($retail_price)
	{ return $this->exth_header->set_retail_price($retail_price); }
	public function remove_retail_price() { return $this->exth_header->remove_retail_price(); }

	/* Retail Price Currency */
	public function retail_price_currency() { return $this->exth_header->retail_price_currency(); }
	public function set_retail_price_currency($retail_price_currency)
	{ return $this->exth_header->set_retail_price_currency($retail_price_currency); }
	public function remove_retail_price_currency()
	{ return $this->exth_header->remove_retail_price_currency(); }

	/* Dictionary Short Name */
	public function dictionary_short_name()
	{ return $this->exth_header->dictionary_short_name(); }
	public function set_dictionary_short_name($dictionary_short_name)
	{ return $this->exth_header->set_dictionary_short_name($dictionary_short_name); }
	public function remove_dictionary_short_name()
	{ return $this->exth_header->remove_dictionary_short_name(); }

	/* Cover Offset */
	public function cover_offset() { return $this->exth_header->cover_offset(); }
	public function set_cover_offset($cover_offset)
	{ return $this->exth_header->set_cover_offset($cover_offset); }
	public function remove_cover_offset() { return $this->exth_header->remove_cover_offset(); }

	/* Thumbnail Offset */
	public function thumbnail_offset() { return $this->exth_header->thumbnail_offset(); }
	public function set_thumbnail_offset($thumbnail_offset)
	{ return $this->exth_header->set_thumbnail_offset($thumbnail_offset); }
	public function remove_thumbnail_offset()
	{ return $this->exth_header->remove_thumbnail_offset(); }

	/* Creator Software */
	public function creator_software() { return $this->exth_header->creator_software(); }
	public function set_creator_software($creator_software)
	{ return $this->exth_header->set_creator_software($creator_software); }
	public function remove_creator_software()
	{ return $this->exth_header->remove_creator_software(); }
	public function creator_software_str() { return $this->exth_header->creator_software_str(); }

	/* Creator Major */
	public function creator_major() { return $this->exth_header->creator_major(); }
	public function set_creator_major($creator_major)
	{ return $this->exth_header->set_creator_major($creator_major); }
	public function remove_creator_major()
	{ return $this->exth_header->remove_creator_major(); }

	/* Creator Minor */
	public function creator_minor() { return $this->exth_header->creator_minor(); }
	public function set_creator_minor($creator_minor)
	{ return $this->exth_header->set_creator_minor($creator_minor); }
	public function remove_creator_minor()
	{ return $this->exth_header->remove_creator_minor(); }

	/* Creator Build */
	public function creator_build() { return $this->exth_header->creator_build(); }
	public function set_creator_build($creator_build)
	{ return $this->exth_header->set_creator_build($creator_build); }
	public function remove_creator_build()
	{ return $this->exth_header->remove_creator_build(); }

	/* CDE Type */
	public function cde_type() { return $this->exth_header->cde_type(); }
	public function set_cde_type($cde_type)
	{ return $this->exth_header->set_cde_type($cde_type); }
	public function remove_cde_type()
	{ return $this->exth_header->remove_cde_type(); }
	public function cde_type_str() { return $this->exth_header->cde_type_str(); }

	/* Updated Title */
	public function updated_title() { return $this->exth_header->updated_title(); }
	public function set_updated_title($updated_title)
	{ return $this->exth_header->set_updated_title($updated_title); }
	public function remove_updated_title()
	{ return $this->exth_header->remove_updated_title(); }

	/* Language */
	public function language() { return $this->exth_header->language(); }
	public function set_language($language)
	{ return $this->exth_header->set_language($language); }
	public function remove_language()
	{ return $this->exth_header->remove_language(); }

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
	 * Set title.
	 * Sets full name and updated title.
	 * @param $title title.
	 * @return title.
	 */
	public function set_title($title)
	{
		$this->set_pdb_name($title);
		$this->set_updated_title($title);

		return $this->set_full_name($title);
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

	/**
	 * Add a PalmDOC text record.
	 * @param $text text of record.
	 * @return text record.
	 */
	public function add_text_record($text)
	{
		$this->mobi_header->first_content_index = 1;
		$this->mobi_header->last_content_index++;
		$this->mobi_header->first_non_book_index++;

		return parent::add_text_record($text);
	}

	/**
	 * Remove all PalmDOC text records.
	 * @return true if removed.
	 */
	public function remove_text_records()
	{
		$record_count = $this->palmdoc_header->record_count;

		$this->mobi_header->first_content_index = 1;
		$this->mobi_header->last_content_index -= $record_count;
		$this->mobi_header->first_non_book_index = 1;

		return parent::remove_text_records();
	}

	/**
	 * Get the MOBIPocket html.
	 * @return html.
	 */
	public function html()
	{
		if ($html = $this->text())
		{
			/* Add anchors at filepos. */
			$html = self::html_anchors($html);

			/* Add charset for correct dom parsing. */
			$charset = $this->mobi_header->text_encoding_str();
			$html = preg_replace('/<head>/', 
			   '<head><meta http-equiv="Content-type" content="text/html; charset=' . $charset . '"/>', 
			   $html);

			$doc = new DOMDocument();
			libxml_use_internal_errors(true);
			$doc->loadHTML($html);
			libxml_clear_errors();

			/* Anchors: Replace 'filepos' with 'href'. */
			foreach ($doc->getElementsByTagName('a') as $anchor)
			{
				if ($filepos = $anchor->getAttribute('filepos'))
				{
					$anchor->removeAttribute('filepos');
					$anchor->setAttribute('href', 
					   '#' . $filepos);
				}
			}

			/* Images: Replace 'recindex' with 'src'. */
			foreach ($doc->getElementsByTagName('img') as $img)
			{
				if ($img->hasAttribute('alt'))
					$img->removeAttribute('alt');

				if ($recindex = $img->getAttribute('recindex'))
				{
					$img->removeAttribute('recindex');
					$recindex = $this->image_record_offset2index($recindex - 1);
					if ($image = $this->image_record($recindex))
						$img->setAttribute('src', 'data:image/jpg;base64,' . base64_encode($image));
				}
			}

			return $doc->saveHTML();
		}

		return "";
	}

	/**
	 * Add html anchors at 'filepos' in MOBIPocket html.
	 * @param $html MOBIPocket html.
	 * @return html.
	 */
	private static function html_anchors($html)
	{
		$positions = self::anchor_filepos($html);

		$new_html = '';
		$prev_pos = 0;
		foreach ($positions as $position)
		{
			$new_html .= substr($html, $prev_pos, 
			   $position - $prev_pos);
			$new_html .= '<a id="' . $position . '"></a>';
			$prev_pos = $position;
		}
		$new_html .= substr($html, $prev_pos, strlen($html));

		return $new_html;
	}

	/**
	 * Get array of unique 'filepos' from MOBIPocket html.
	 * @param $html MOBIPocket html.
	 * @return array of filepos.
	 */
	private static function anchor_filepos($html)
	{
		$positions = array();
		if (preg_match_all('/filepos=(\d+)/', $html, $positions))
		{
			$positions = array_unique($positions[1], SORT_NUMERIC);
			sort($positions, SORT_NUMERIC);
			return $positions;
		}

		return array();
	}
}

?>
