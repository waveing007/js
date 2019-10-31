<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

//insert.php

include('database_connection.php');

$form_data = json_decode(file_get_contents("php://input"));


$idd ='';
$error = '';
$message = '';
$validation_error = '';
$first_name = '';
$last_name = '';
$sub_id = '';
$address = '';

if($form_data->action == 'fetch_single_data')
{
	$query = "SELECT * FROM tbl_sample WHERE id='".$form_data->id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['first_name'] = $row['first_name'];
		$output['last_name'] = $row['last_name'];
		
	}
}
elseif($form_data->action == "Delete")
{
	$query = "
	DELETE FROM tbl_sample WHERE id='".$form_data->id."'
	";
	$statement = $connect->prepare($query);
	if($statement->execute())
	{
		$output['message'] = 'Data Deleted';
	}
}
else
{
	if(empty($form_data->first_name))
	{
		$error[] = 'First Name is Required';
	}
	else
	{
		$first_name = $form_data->first_name;
	}

	if(empty($form_data->last_name))
	{
		$error[] = 'Last Name is Required';
	}
	else
	{
		$last_name = $form_data->last_name;
	}
	if(empty($form_data->sub_id))
	{
		$error[] = 'Subject id is Required';
	}
	else
	{
		$sub_id = $form_data->sub_id;
	}
	if(empty($form_data->address))
	{
		$error[] = 'address is Required';
	}
	else
	{
		$address = $form_data->address;
	}

	if(empty($error))
	{
		if($form_data->action == 'Insert')
		{
			$data = array(
				':id'				=>	$form_data->idd,
				':first_name'		=>	$first_name,
				':last_name'		=>	$last_name
				':sub_id'			=>	$sub_id,
				':address'			=>	$address
			);
			$query = "
			INSERT INTO `tbl_sample` (`id`, `first_name`, `last_name`, `sub_id`, `address`) VALUES 
				(:id, :first_name, :last_name, :sub_id, :address)
			";
			$statement = $connect->prepare($query);
			if($statement->execute($data))
			{
				$message = 'Data Inserted';
			}
		}
		if($form_data->action == 'Edit')
		{
			$data = array(
				':first_name'	=>	$first_name,
				':last_name'	=>	$last_name,
				':id'			=>	$form_data->idd
				':sub_id'		=>	$sub_id,
				':address'		=>	$address
			);
			$query = "
			UPDATE tbl_sample 
			SET first_name = :first_name, last_name = :last_name, sub_id = :sub_id, address = :address 
			WHERE id = :id
			";

			$statement = $connect->prepare($query);
			if($statement->execute($data))
			{
				$message = 'Data Edited';
			}
		}
	}
	else
	{
		$validation_error = implode(", ", $error);
	}

	$output = array(
		'error'		=>	$validation_error,
		'message'	=>	$message
	);

}



echo json_encode($output);

?>