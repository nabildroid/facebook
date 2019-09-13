<?php
namespace Facebook;
require __DIR__ . '/vendor/autoload.php';

$user=new Account;
$user->login("datr=AZFIXQTAaRVWnVEXWdFAmepU; sb=AZFIXQzp0jJDVApP-vpXwKow; m_pixel_ratio=1; locale=en_US; x-referer=eyJyIjoiL2dyb3Vwcy80OTI3MDMyOTc1MjU0MDYiLCJoIjoiL2dyb3Vwcy80OTI3MDMyOTc1MjU0MDYiLCJzIjoibSJ9; wd=1351x672; ; c_user=100035861824006; xs=34%3AUXzkKRuqOIA2Rw%3A2%3A1568036259%3A629%3A12765; fr=0SWZUCvrUfbSKcUnY.AWWLqiiy1lPyMqbpaUiur3vtvWY.BdSI35.hv.AAA.0.0.BddlWj.AWWohHjP");


$test="wall";

include "tests/test_".$test.".php";

?>