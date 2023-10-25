<?php
/*
Template Name: TEST
*/

//$res = (new RestApiGis)->getUserByEmail('777soten@mail.ru');

$RestApiGis = new RestApiGis();
$user = new GetUser();

//$fields = $RestApiGis->newUser(
//    $RestApiGis->arrayDataUserToSendGis(
//        $user->getUserByLogin('maksim')
//    )
//);

$fields = $RestApiGis->getGroupsGis();

log_to_console($fields);

?>
<pre>
   <?php print_r($fields); ?>
</pre>


