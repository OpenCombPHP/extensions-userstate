<?php 
return array(
		'class' => 'model' ,
		'orm' => array(
			'table' => 'state' ,
	        'keys'=>array('stid') ,
	        'alias' => array(
	                'info.nickname' => 'nickname' ,
	        ),
			'hasOne:info' => array(    //一对一
				'table' => 'coresystem:userinfo',
				'fromkeys'=>'uid',
				'tokeys'=>'uid',
                //'columns' => '*' ,        
			) ,
    		'hasMany:attachments'=>array(    //一对多
    				'fromkeys'=>'stid',
    				'tokeys'=>'stid',
    		        'table'=>'state_attachment',
    		)
		) ,
);