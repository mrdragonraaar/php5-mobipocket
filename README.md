#PHP5 MOBIPocket

PHP5 library for reading MOBIPocket files.

## Usage

**Include**

    include_once('mobipocket/mobipocket.php');
    
**New Instance**

    $mobipocket = new mobipocket();
    
**Read MOBIPocket**

    if ($fh = fopen($this->filename, "r"))
    {
        $mobipocket->read($fh);

        fclose($fh);
    }
    
**Output**

    $text = $mobipocket->text();
    $html = $mobipocket->html();
    
**Metadata**

    $title = $mobipocket->title();
    
    foreach ($mobipocket->authors() as $author)
        echo $author;
    
    $description = $mobipocket->description();
    
    $publisher = $mobipocket->publisher();
    if ($mobipocket->publishing_date())
    {
        $date_format = 'Y-m-d';
        $publishing_date_str = $mobipocket->publishing_date_str($date_format);
        echo $publishing_date_str;
    }
    
    $isbn = $mobipocket->isbn();
    
    foreach ($mobipocket->subjects() as $subject)
        echo $subject;
        
    $language = $mobipocket->language();
    
    $contributor = $mobipocket->contributor();
    
    if ($mobipocket->creator_software())
    {
        $creator_software_str = $mobipocket->creator_software_str();
        echo $creator_software_str;
        echo $mobipocket->creator_major();
        echo $mobipocket->creator_minor();
        echo $mobipocket->creator_build();
    }
    
    $cover = $mobipocket->cover();
    $thumbnail = $mobipocket->thumbnail();
    
    
    
