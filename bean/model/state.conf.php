<?php 
return array(
		'class' => 'model' ,
		'orm' => array(
			'table' => 'state' ,
			'hasOne:info' => array(    //一对一
				'table' => 'coresystem:userinfo'
			) ,
    		'hasMany:attachments'=>array(    //一对多
    				'fromkeys'=>'stid',
    				'tokeys'=>'stid',
    		        'table'=>'state_attachment',
    		)
		) ,
);