<?php

include('database_connection.php');

$form_data = json_decode(file_get_contents("php://input"));

    $error = array();
    $message = '';
    $validation_error = '';
    $first_name = '';
    $last_name = '';
    $output = array();
    if($form_data->action == 'fetch_single_data'){
        
        $query="SELECT * FROM  tbl_sample WHERE id='".$form_data->id."'";
        $statement = $connect->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $raw){
            $output['first_name']= $raw['first_name'];
            $output['last_name']= $raw['last_name'];
        }
    }elseif($form_data->action == 'Delete'){
       
        $statement = $connect->prepare("
                    DELETE FROM tbl_sample WHERE id = ?
                    ");
        $statement->bindParam(1, $form_data->id, PDO::PARAM_INT);

         if($statement->execute())
        {
           $message = 'Data Deleted';
        }              
    }else{

                if(empty($form_data->first_name))
                {
                    array_push($error,'First Name is required');
                }else{
                    $first_name = $form_data->first_name;
                }
                if(empty($form_data->last_name))
                {
                    array_push($error,'Last Name is required');
                }else{
                    $last_name = $form_data->last_name;
                }
                if(empty($error)){
                    if($form_data->action == 'Insert')
                    {
                        $statement = $connect->prepare("
                        INSERT INTO tbl_sample
                            (first_name,last_name) VALUES
                            (?,?)
                        ");
                        $statement->bindParam(1, $first_name, PDO::PARAM_STR);
                        $statement->bindParam(2, $last_name, PDO::PARAM_STR);
                        
                        if($statement->execute()){
                            $message = 'Data Inserted';
                        }    
                    }

                    if($form_data->action == 'Edit')
                    { 
                        $statement = $connect->prepare("
                        UPDATE tbl_sample 
                        SET first_name = ?, last_name = ?
                        WHERE id = ?
                        ");
                        $statement->bindParam(1, $first_name, PDO::PARAM_STR);
                        $statement->bindParam(2, $last_name, PDO::PARAM_STR);
                        $statement->bindParam(3, $form_data->id, PDO::PARAM_INT);

                        if($statement->execute())
                        {
                            $message = 'Data Edited';
                        }
                    }
                }else {
                    $validation_error = implode(",",$error);
                }
            }
   
    $output['error']= $validation_error;
    $output['message']= $message;

    echo json_encode($output);
?>
