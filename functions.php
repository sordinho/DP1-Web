<?php
define('ROW_NUMBER', 10);
define('COL_NUMBER', 6);

function printSeatTable($clickable)
{
    $letters = range('A', 'Z');
    if ($clickable==false)
        $buttonAttribute='disabled';
    else
        $buttonAttribute=" ";
    for ($i=1; $i<=ROW_NUMBER; $i++ ){
        echo "<tr>";
        for ($j=1;$j<=COL_NUMBER; $j++){
            $buttonName = $letters[$i-1]."_".$j;
            //echo "<td>". $buttonName ."</td>";

            echo '<td><button type="button" '.$buttonAttribute.' class="soldSeat" name="'.$buttonName.'">'.$buttonName.'</button></td>';
        }
        echo "</tr>";
    }


}

?>
