#PHP5 MOBIPocket

PHP5 library for reading MOBIPocket files.

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
    
    
    
