<?php
  $records = array();
  function myRecordHandler($record)
  {
    global $records;
    $records[] = $record;
  }
  $url = "http://www.example.com/api.asp?q=".urlencode($q);
  MagicParser_parse($url,"myRecordHandler","xml|PRODUCTRESPONSE/OFFERS/OFFER/");
  function myDisplayRecordHandler($record)
  {
    // original myRecordHandler function to display results
  }
  if (count($records))
  {
    print "<h2>Example.com Results</h2>";
    foreach($records as $record)
    {
      myRecordHandler($record);
    }
  }
?>