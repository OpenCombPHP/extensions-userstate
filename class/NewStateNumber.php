<?php
namespace org\opencomb\userstate ;

use org\jecat\framework\db\DB;

use org\jecat\framework\bean\BeanFactory;

use org\jecat\framework\auth\IdManager;
use org\jecat\framework\message\Message;
use org\opencomb\coresystem\mvc\controller\Controller ;

class NewStateNumber extends Controller
{
	public function createBeanConfig()
	{
	    
	    $aOrm = array(
	    
	    /**
	     * 模型
	    * list = true 返回多条记录
	    */
	            'model:state' => array(
	                    'class' => 'model' ,
	                    'orm' => array(
                                'columns' => array("system","stid","forwardtid","replytid","uid","title","body","article_title","article_uid","time","data","client") ,  
	                            'table' => 'userstate:state' ,
                    			'hasMany:astate' => array(    //一对一
                                    'columns' => array("service","sid","pullcommenttime","old_comment_page") ,   
                    				'table' => 'oauth:state',
                    				'fromkeys'=>'stid',
                    				'tokeys'=>'stid',
                		            'keys'=>array('service','sid'),
                    			) , 
	                    ) ,
	                    'list'=>true,
	            ) ,
	    
	    ) ;
	    
	    
	    if( $this->params["channel"] == "friends")
	    {
	    $aId = $this->requireLogined() ;
	        
	        $aOrm['model:state'] = array(
            		'class' => 'model' ,
            		'orm' => array(
            			'table' => 'userstate:state' ,
                        'columns' => array("system","stid","forwardtid","replytid","uid","title","body","article_title","article_uid","time","data","client") ,  
            			'hasMany:astate' => array(    //一对一
                            'columns' => array("service","sid","pullcommenttime","old_comment_page") ,  
            				'table' => 'oauth:state',
            				'fromkeys'=>'stid',
            				'tokeys'=>'stid',
        		            'keys'=>array('service','sid'),
            			) , 
    			        'hasOne:subscription'=>array(    //一对多
    			                'keys'=>array('from','to') ,
    			                'fromkeys'=>'uid',
    			                'tokeys'=>'to',
    			                'table'=>'friends:subscription',
    			        ),
            		) ,
                    'list'=>true,
            );
	        
	        $aUid = array();
	        foreach (IdManager::singleton()->iterator() as $v){
	            $aUid[] = $v->userId();
	        }
	        if(count($aUid) > 1)
	        {
	        	$sSql = '';
	        	foreach($aUid as $nKey => $nUid)
	        	{
	        		if($nKey)
	        		{
	        			$sSql .= ',';
	        		}
	        		$sSql.='@'.($nKey+1);
	        	}
	        	$aUid[] =  $aId->userId();
	        	
	            $aOrm['model:state']['orm']['where'] = array( "(subscription.from in ( {$sSql} ) or uid = @" . count($aUid)+1 .')', $aUid );
	        }else{
	            $aOrm['model:state']['orm']['where'] = array( '(subscription.from = @1 or uid = @2)' , $aId->userId() , $aId->userId() );
	        }
	    }
	    
	    
	    if( $this->params["channel"] == "wownei")
	    {
	        //$aId = $this->requireLogined() ;
	        $aOrm['model:state'] = array(
	                'class' => 'model' ,
	                'orm' => array(
                            'columns' => array("system","stid","forwardtid","replytid","uid","title","body","article_title","article_uid","time","data","client") ,  
	                        'table' => 'userstate:state' ,
                			'hasMany:astate' => array(    //一对一
                                'columns' => array("service","sid","pullcommenttime","old_comment_page") ,  
                				'table' => 'oauth:state',
                				'fromkeys'=>'stid',
                				'tokeys'=>'stid',
            		            'keys'=>array('service','sid'),
                			) , 
	                        'hasOne:subscription'=>array(    //一对多
	                                'keys'=>array('from','to') ,
	                                'fromkeys'=>'uid',
	                                'tokeys'=>'to',
	                                'table'=>'friends:subscription',
	                        ),
	                        'where' => array( 'stid not like @1',"pull|%") ,
	                ) ,
	                'list'=>true,
	        );
	    
	    }
	    
	    return  $aOrm;
	}
	
	public function process()
	{
	    $oState = new State();
	    $sSql = '';
	    $arrParamsForSql = array();
	    
        if($this->params["system"])
        {
            $sSql.= 'system = @1';
            $arrParamsForSql[] = $this->params["system"];
        }
        if($this->params["service"])
        {
            if($this->params["service"] == "wownei.com"){
                $sSql[] = "stid not like 'pull|%'" ;
            }else{
                $sSql[] = 'astate.service = @' . (count($sSql)+1);
                $arrParamsForSql[] = $this->params["service"];
            }
        }
        if($this->params["sex"])
        {
        	$nSqlNum = 1;
			if(!empty($sSql)){
				$sSql.= ' and ';
				$nSqlNum = 2;
			}
            $sSql.= 'info.sex = @' . $nSqlNum;
            $arrParamsForSql[] = $this->params["sex"];
        }
        
        $this->state->prototype()->criteria()->addOrderBy('time',true);
        $this->state->prototype()->criteria()->where()->gt('time',$this->params['time']);
        $this->state->setPagination(1000,1);
        
	    $this->state->loadSql($sSql,$arrParamsForSql) ;
	    echo $this->state->childrenCount();exit;
	    
	    /**
	     * 打印model
	     * $this->state->printStruct();
	     */
	}
}