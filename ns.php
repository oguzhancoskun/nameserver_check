<!DOCTYPE html>
<!-- ##############################################################
#       +ns_check tool:                                           #
#               #nameserver check tool.                           #
#       +dependencies:                                            #
#               #mysql 5.5>                                       #
#               #php 5.4>                                         #
#                                                                 #
#       +installing:                                              #
#               #create database and table                        #
#               #table informations: "table_name dns_list"        #
#                       *id int primary key                       #
#                       *domain varchar()                         #
#                       *nameserver varchar()                     #
#                       *status bool                              #
#               #change database connection information to line20.#
#               #ready!                                           #
############################################################### -->
<?php
$conn = new PDO("mysql:host=<hostname>; dbname=<dbname>",'<username>','<password>');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$fpon=0;
$content= '';
$ss=0;
foreach ($conn->query('SELECT domain,nameserver FROM dns_list') as $row) {

    $match = (string)$row['domain'];
    $nmatch = (string)$row['nameserver'];

    $chnm='';

        $xx = explode('.', $nmatch, 2);

        $chnm = $xx[1];

    $check_res = dns_get_record($match);

    for ($i =0; $i<count($check_res);$i++){

           if($check_res[$i]['type']=='NS'){

                $mns = (string)$check_res[$i]['target'];

                $xs = explode('.', $mns, 2);

                if($chnm!=$xs[1]){
                        $fpon++;
                }
        }
   }
        if($fpon>0){
         $content.=$match."\n";
	$fpon=0;
	$ss++;
	}
}
if($ss>0){
 $to = 'sunucu@ozguryazilim.com.tr';
 $subject = 'Name Server Control Info Message (Weekly Report)';
 mail($to,$subject,$content);
}
$ss=0;
$conn=null;
?>

