<?php
namespace Facebook;
require __DIR__ . '/vendor/autoload.php';

$user=new Account;
$user->login("datr=AZFIXQTAaRVWnVEXWdFAmepU; sb=AZFIXQzp0jJDVApP-vpXwKow; m_pixel_ratio=1; locale=en_US; x-referer=eyJyIjoiL2dyb3Vwcy80OTI3MDMyOTc1MjU0MDYiLCJoIjoiL2dyb3Vwcy80OTI3MDMyOTc1MjU0MDYiLCJzIjoibSJ9; wd=1351x672; c_user=100011210498274; xs=39%3App8x3AWfzHJfeA%3A2%3A1568024818%3A629%3A615; fr=0SWZUCvrUfbSKcUnY.AWX_z5P0C80BDkkzK1h6J_e0QM0.BdSI35.hv.AAA.0.0.Bddijx.AWX8HMB3");


$test="wall";

include "tests/test_".$test.".php";

?>