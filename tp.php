<?php
include "dd.php";
for ($i = 1; $i <= 20; $i++) {
    $stager[] = "stager" . " " . $i;
    $note[] = rand(0, 20);
}
for ($i = 0; $i < count($stager); $i++) {
    $stagernote[$stager[$i]] = $note[$i];
}
dd($stagernote);
for ($i=0; $i < count($stager) ; $i++) { 
    echo($stager[$i] . " : " . $note[$i] . "<br>" );
}

print_r($stagernote);
echo("<br>");
foreach ($stagernote as $Key => $note) {
        echo($s);
}


?>