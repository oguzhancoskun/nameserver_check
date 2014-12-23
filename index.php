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
#               #change database connection information to line54.#
#               #ready!                                           #
############################################################### -->
<html>
	<head>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<title>NameServer Check</title>	

	</head>
	<body>

	<h3>NameServer Check Tool</h3>

<form class='form-group' action='index.php' method='post'>
	<div class='form-group'> 
	<input class='text' placeholder='Add Domain Address' type='text' name='srx'/>
	<button class='btn btn-primary' type='submit' value='search'>ADD</button>
	</div>
</form>

<?php

$searchd = $_POST['srx'];
$result = '';
$nameserver = '';

$nlist=array();

if($searchd!=null){
	$result = dns_get_record($searchd);
}

$conn = new PDO("mysql:host=<hostname>; dbname=<dbname>",'<username>','<password>');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try{	

$cntr = 0;
$check = '';

echo '<fieldset><select multiple class="form-control"  id = "selLanguage" size = "6">';

if(result!=''){
	for ($i =0; $i<count($result);$i++){
		if($result[$i]['type']=='NS'){
		
		$nameserver = (string)$result[$i]['target'];  
		echo '<option value = "'.$nameserver.'"><pre>'.$nameserver.'</pre></option>';

				
  	}
    }
}
	echo '</select></fieldset><br>';
//get search nameservers
for ($i =0; $i<count($result);$i++){		

	if($result[$i]['type']=='NS'){  
	
	array_push($nlist,$result[$i]['target']);
	
	}

}
//database domain control
foreach ($conn->query('SELECT domain FROM dns_list') as $row) {

    $check = (string)$row['domain'];

    if ($check==$searchd)
           {
           $cntr++;
           }
    }

//check db & search
if($cntr==0){
  
  foreach ($nlist as $nl){

   $state = $conn->prepare("INSERT INTO dns_list (`domain`,`nameserver`) VALUES (:dname, :names)");
                $state->execute(array(
                        "dname" => $searchd,
                        "names" => $nl
                ));
                }
       
  echo '<pre class="bg-success">New Domain Added!</pre>';
        }
else{
        echo '<pre class="bg-success">This Domain Exist!</pre>';
    }

}
catch(PDOException $e)
	{
		echo $e->getMessage();
	}


$conn=null;
?>
</body>
</html>

