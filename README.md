#PHP5 MOBIPocket

PHP5 library for reading and writing MOBIPocket files.

## Usage

**Include**

    include_once('mobipocket/mobipocket.php');
    
**New Instance**

    $mobipocket = new mobipocket();
    
**Load MOBIPocket**

    $mobipocket->load($filename);
    
**Read MOBIPocket**

    if ($fh = fopen($filename, "r"))
    {
        $mobipocket->read($fh);

        fclose($fh);
    }

**Save MOBIPocket**

    $mobipocket->save($new_filename);
    
**Write Mobipocket**

    if ($fh = fopen($new_filename, "w"))
    {
        $mobipocket->write($fh);
        
        fclose($fh);
    }
    
**Text**

    $mobipocket->set_text($new_text);

    $text = $mobipocket->text();
    $html = $mobipocket->html();

**Images**

    $mobipocket->add_image_record($image_data);
    $mobipocket->remove_image_records();
    
    $mobipocket->set_cover($cover_image_data);
    $cover = $mobipocket->cover();

    $mobipocket->set_thumbnail($thumbnail_image_data);
    $thumbnail = $mobipocket->thumbnail();
    
**Metadata**

    $mobipocket->set_title($title);
    $title = $mobipocket->title();
    
    $mobipocket->add_author($author);
    foreach ($mobipocket->authors() as $author)
        echo $author;
    $mobipocket->remove_author();
    
    $mobipocket->set_description($description);
    $description = $mobipocket->description();
    $mobipocket->remove_description();
    
    $mobipocket->set_publisher($publisher);
    $publisher = $mobipocket->publisher();
    $mobipocket->remove_publisher();
    
    $mobipocket->set_publishing_date($publishing_date);
    if ($mobipocket->publishing_date())
    {
        $date_format = 'Y-m-d';
        $publishing_date_str = $mobipocket->publishing_date_str($date_format);
        echo $publishing_date_str;
    }
    $mobipocket->remove_publishing_date();
    
    $mobipocket->set_isbn($isbn);
    $isbn = $mobipocket->isbn();
    $mobipocket->remove_isbn();
    
    $mobipocket->add_subject($subject);
    foreach ($mobipocket->subjects() as $subject)
        echo $subject;
    $mobipocket->remove_subject();
        
    $mobipocket->set_language($language);
    $language = $mobipocket->language();
    $mobipocket->remove_language();
    
    $mobipocket->set_contributor($contributor);
    $contributor = $mobipocket->contributor();
    $mobipocket->remove_contributor();
    
    $mobipocket->set_creator_software($creator_software);
    $mobipocket->set_creator_major($creator_major);
    $mobipocket->set_creator_minor($creator_minor);
    $mobipocket->set_creator_build($creator_build);
    if ($mobipocket->creator_software())
    {
        $creator_software_str = $mobipocket->creator_software_str();
        echo $creator_software_str;
        echo $mobipocket->creator_major();
        echo $mobipocket->creator_minor();
        echo $mobipocket->creator_build();
    }
    $mobipocket->remove_creator_software();
    $mobipocket->remove_creator_major();
    $mobipocket->remove_creator_minor();
    $mobipocket->remove_creator_build();
    
    
