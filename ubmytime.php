<?php
/*
Project     : Ub MyTime - PHP MySQL date/time study
Purpose     : Perform date/time calculations in a MySQL table with records about the hours you spent on the job ;-)
File name   : ubmytime.php
Version     : 1.0.061116
Author(s)   : Radu Maier
Copyright   : Copyright (C) 2006 Radu Maier oimoroiATgmailDOTcom
License     : GNU/GPL
Website     : http://underbeat.evonet.ro

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public  License for more details.

You should have received a copy of the GNU General Public License
along with this program from the site that you downloaded it
from; if not. write to the Free Software Foundation, Inc., 59 Temple
Place, Suite 330, Boston, MA 02111-1307 USA
*/
?>
<PRE>
<?php
// database configuration file
include './include/config.inc.php';

// MyDate function
include './ubmydate.php';

// open the db connection
include './include/dbopen.inc.php';

// for the beginning let's see how many days we worked so far....
$query_rst_days_worked = "SELECT * FROM `briantecs`";
$rst_days_worked = mysql_query($query_rst_days_worked) or die('Oups!<br>' . mysql_error());
$days_worked = mysql_num_rows($rst_days_worked);

// total hours worked
$query_rst_total_hours = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(`end_time`, `start_time`)))) FROM `briantecs`";
$rst_total_hours = mysql_query($query_rst_total_hours) or die('Oups!<br>' . mysql_error());
$total_hours = mysql_result($rst_total_hours, 0);

for ($i=1; $i<=mysql_num_rows($rst_days_worked); $i++) {
    $row_rst_days_worked = mysql_fetch_assoc($rst_days_worked);

    // uniquely identify each day
    $day_id = $row_rst_days_worked['id'];

    // get the day of the week from the date
    $query_rst_day_name = "SELECT WEEKDAY(`work_day`) FROM `briantecs` WHERE `id` = '$day_id' LIMIT 1";
    $rst_day_name = mysql_query($query_rst_day_name) or die('Oups!<br>' . mysql_error());
    $day_name = mysql_result($rst_day_name, 0);

    // how many hours we've worked each day?
    $query_rst_hours_a_day = "SELECT TIMEDIFF(`end_time`, `start_time`) FROM `briantecs` WHERE `id` = '$day_id' LIMIT 1";
    $rst_hours_a_day = mysql_query($query_rst_hours_a_day) or die('Oups!<br>' . mysql_error());
       
    if (mysql_result($rst_hours_a_day, 0) == '00:00:00') {$hours_a_day = 'no work today!';}
    else {$hours_a_day = mysql_result($rst_hours_a_day, 0);}

    switch ($day_name) {
        case "0": {$day_name = 'Monday    '; break;}
        case "1": {$day_name = 'Tuesday   '; break;}
        case "2": {$day_name = 'Wednesday '; break;}
        case "3": {$day_name = 'Thursday  '; break;}
        case "4": {$day_name = 'Friday    '; break;}
        case "5": {$day_name = 'Saturday  '; break;}
        case "6": {$day_name = '<FONT COLOR="#990000">Sunday    </FONT>'; break;}
        default: {break;}
    }

    echo $i . '.&nbsp;' . $day_name . '&nbsp;';
    // this is the only place we use the MyDate function:
    MyDate($row_rst_days_worked['work_day'], "dd/mm");
    echo '&nbsp;' . $hours_a_day;
    // start a new row only if we have more days
    if($i != mysql_num_rows($rst_days_worked)) {echo '<BR>';}

    // free query results from mmory
    mysql_free_result($rst_hours_a_day);
    mysql_free_result($rst_day_name);
}

// show us how many days we've worked so far
echo '<HR WIDTH="230" ALIGN="left">';
echo 'days worked:&nbsp;<B>' . $days_worked . '</B>';
echo '<BR>';
echo 'hours worked:&nbsp;<B>' . $total_hours . '</B>';

// free remaining query results from mmory
mysql_free_result($rst_total_hours);
mysql_free_result($rst_days_worked);

// close the db connection
include './include/dbclose.inc.php';
?>
</PRE>